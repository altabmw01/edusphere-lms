<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\Course\StoreCourseRequest;
use App\Http\Requests\Course\UpdateCourseRequest;
use App\Models\Category;
use App\Models\Course;
use App\Services\CourseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CourseController extends Controller
{
    use AuthorizesRequests;
    
    public function __construct(protected CourseService $courseService)
    {
    }

    public function index(): View
    {
        return view('teacher.courses.index', [
            'courses' => $this->courseService->forTeacher(auth()->user()),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Course::class);

        return view('teacher.courses.create', [
            'categories' => Category::type('course')->active()->ordered()->get(),
        ]);
    }

    public function store(StoreCourseRequest $request): RedirectResponse
    {
        // Teacher-submitted courses go to "pending" for manager/admin review, never straight to published.
        $data = $request->safe()->except(['thumbnail', 'banner']);
        $data['status'] = $data['status'] === 'published' ? 'pending' : $data['status'];

        $course = $this->courseService->create($data, $request->user(), $request->file('thumbnail'), $request->file('banner'));

        return redirect()->route('teacher.courses.edit', $course)->with('status', 'Course submitted for review.');
    }

    public function edit(Course $course): View
    {
        $this->authorize('update', $course);

        return view('teacher.courses.edit', [
            'course' => $course->load('sections.lessons'),
            'categories' => Category::type('course')->active()->ordered()->get(),
        ]);
    }

    public function update(UpdateCourseRequest $request, Course $course): RedirectResponse
    {
        $this->courseService->update($course, $request->safe()->except(['thumbnail', 'banner']), $request->file('thumbnail'), $request->file('banner'));

        return back()->with('status', 'Course updated.');
    }

    public function destroy(Course $course): RedirectResponse
    {
        $this->authorize('delete', $course);
        $this->courseService->delete($course);

        return redirect()->route('teacher.courses.index')->with('status', 'Course deleted.');
    }
}
