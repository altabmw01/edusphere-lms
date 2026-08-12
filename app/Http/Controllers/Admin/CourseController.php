<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Course\StoreCourseRequest;
use App\Http\Requests\Course\UpdateCourseRequest;
use App\Models\Category;
use App\Models\Course;
use App\Models\User;
use App\Services\CourseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CourseController extends Controller
{
	use AuthorizesRequests;
	
    public function __construct(protected CourseService $courseService)
    {
    }

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Course::class);

        return view('admin.courses.index', [
            'courses' => $this->courseService->forAdmin($request->only(['search', 'status'])),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Course::class);

        return view('admin.courses.create', [
            'categories' => Category::type('course')->active()->ordered()->get(),
            'teachers' => User::role(User::ROLE_TEACHER)->active()->orderBy('name')->get(),
        ]);
    }

    public function store(StoreCourseRequest $request): RedirectResponse
    {
        $teacher = User::findOrFail($request->input('teacher_id', $request->user()->id));

        $course = $this->courseService->create(
            $request->safe()->except(['thumbnail', 'banner', 'teacher_id']),
            $teacher,
            $request->file('thumbnail'),
            $request->file('banner'),
        );

        return redirect()->route('admin.courses.edit', $course)->with('status', 'Course created successfully.');
    }

    public function edit(Course $course): View
    {
        $this->authorize('update', $course);

        return view('admin.courses.edit', [
            'course' => $course->load('sections.lessons'),
            'categories' => Category::type('course')->active()->ordered()->get(),
            'teachers' => User::role(User::ROLE_TEACHER)->active()->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateCourseRequest $request, Course $course): RedirectResponse
    {
        $this->courseService->update(
            $course,
            $request->safe()->except(['thumbnail', 'banner']),
            $request->file('thumbnail'),
            $request->file('banner'),
        );

        return back()->with('status', 'Course updated successfully.');
    }

    public function destroy(Course $course): RedirectResponse
    {
        $this->authorize('delete', $course);
        $this->courseService->delete($course);

        return redirect()->route('admin.courses.index')->with('status', 'Course deleted.');
    }
}
