<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\BatchLevel;
use App\Models\Book;
use App\Models\BookPurchase;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class BatchController extends Controller
{
	use AuthorizesRequests;
	
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Batch::class);

        $batches = Batch::query()
            ->with(['batchable', 'teacher', 'batchLevel'])
            ->when($request->filled('type'), fn ($q) => $q->where('batchable_type', $request->string('type') === 'book' ? Book::class : Course::class))
            ->when($request->filled('teacher_id'), fn ($q) => $q->where('teacher_id', $request->integer('teacher_id')))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.batches.index', [
            'batches' => $batches,
            'teachers' => User::role(User::ROLE_TEACHER)->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Batch::class);

        return view('admin.batches.create', $this->formData());
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Batch::class);

        $data = $this->validated($request);
        $data['batchable_type'] = $data['batchable_type'] === 'book' ? Book::class : Course::class;
        $data['added_by'] = $request->user()->id;
        $data['updated_by'] = $request->user()->id;

        Batch::create($data);

        return redirect()->route('admin.batches.index')->with('status', 'Batch created and teacher assigned.');
    }

    public function edit(Batch $batch): View
    {
        $this->authorize('update', $batch);

        return view('admin.batches.edit', ['batch' => $batch, ...$this->formData()]);
    }

    public function update(Request $request, Batch $batch): RedirectResponse
    {
        $this->authorize('update', $batch);

        $data = $this->validated($request);
        $data['batchable_type'] = $data['batchable_type'] === 'book' ? Book::class : Course::class;
        $data['updated_by'] = $request->user()->id;

        $batch->update($data);

        return back()->with('status', 'Batch updated.');
    }

    public function destroy(Batch $batch): RedirectResponse
    {
        $this->authorize('delete', $batch);

        $batch->delete();

        return redirect()->route('admin.batches.index')->with('status', 'Batch deleted.');
    }

    /**
     * Show purchasers of this batch's course/book who aren't yet assigned to any
     * batch, so the admin can assign them into this one.
     */
    public function assignStudents(Batch $batch): View
    {
        $this->authorize('update', $batch);

        if ($batch->batchable_type === Course::class) {
            $unassigned = CourseEnrollment::where('course_id', $batch->batchable_id)
                ->whereNull('batch_id')
                ->with('user')
                ->get();
        } else {
            $unassigned = BookPurchase::where('book_id', $batch->batchable_id)
                ->whereNull('batch_id')
                ->with('user')
                ->get();
        }

        return view('admin.batches.assign-students', ['batch' => $batch, 'unassigned' => $unassigned]);
    }

    public function storeAssignStudents(Request $request, Batch $batch): RedirectResponse
    {
        $this->authorize('update', $batch);

        $data = $request->validate([
            'enrollment_ids' => ['nullable', 'array'],
            'enrollment_ids.*' => ['integer'],
        ]);

        $ids = $data['enrollment_ids'] ?? [];

        DB::transaction(function () use ($batch, $ids) {
            if ($batch->batchable_type === Course::class) {
                CourseEnrollment::whereIn('id', $ids)->update(['batch_id' => $batch->id]);
            } else {
                BookPurchase::whereIn('id', $ids)->update(['batch_id' => $batch->id]);
            }
        });

        return back()->with('status', count($ids) . ' student(s) assigned to this batch.');
    }

    protected function formData(): array
    {
        return [
            'courses' => Course::orderBy('title')->get(['id', 'title']),
            'books' => Book::orderBy('title')->get(['id', 'title']),
            'teachers' => User::role(User::ROLE_TEACHER)->orderBy('name')->get(),
            'batchLevels' => BatchLevel::active()->orderBy('name')->get(),
            'days' => Batch::DAYS,
        ];
    }

    protected function validated(Request $request): array
    {
        $validated = $request->validate([
            'batchable_type' => ['required', 'in:course,book'],
            'batchable_id' => [
                'required',
                'integer',
                \Illuminate\Validation\Rule::exists(
                    $request->input('batchable_type') === 'book' ? 'books' : 'courses',
                    'id'
                ),
            ],
            'teacher_id' => ['required', 'exists:users,id'],
            'batch_level_id' => ['nullable', 'exists:batch_levels,id'],
            'batch_number' => ['required', 'string', 'max:50'],
            'batch_name' => ['required', 'string', 'max:150'],
            'class_start_time' => ['required', 'date_format:H:i'],
            'class_end_time' => ['required', 'date_format:H:i', 'after:class_start_time'],
            'batch_days' => ['required', 'array', 'min:1'],
            'batch_days.*' => ['in:Mon,Tue,Wed,Thu,Fri,Sat,Sun'],
            'weekly_days' => ['nullable', 'integer', 'min:1', 'max:7'],
            'batch_started_date' => ['required', 'date'],
            'batch_end_date' => ['nullable', 'date', 'after_or_equal:batch_started_date'],
            'student_limit' => ['required', 'integer', 'min:1'],
            'free_or_paid' => ['boolean'],
            'upcoming_status' => ['boolean'],
            'hide_batch' => ['boolean'],
            'status' => ['boolean'],
        ]);

        $validated['weekly_days'] = $validated['weekly_days'] ?? count($validated['batch_days']);

        return $validated;
    }
}
