<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseLesson;
use App\Models\CourseSection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CurriculumController extends Controller
{
    use AuthorizesRequests;
    
    public function edit(Course $course): View
    {
        $this->authorize('manageCurriculum', $course);

        return view('teacher.courses.curriculum', ['course' => $course->load('sections.lessons')]);
    }

    public function storeSection(Request $request, Course $course): RedirectResponse
    {
        $this->authorize('manageCurriculum', $course);

        $data = $request->validate(['title' => ['required', 'string', 'max:255']]);
        $data['sort_order'] = $course->sections()->max('sort_order') + 1;

        $course->sections()->create($data);

        return back()->with('status', 'Section added.');
    }

    public function storeLesson(Request $request, Course $course, CourseSection $section): RedirectResponse
    {
        $this->authorize('manageCurriculum', $course);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:video,text,pdf,quiz'],
            'content_file' => ['nullable', 'file', 'max:51200'],
            'content_text' => ['nullable', 'string'],
            'duration_minutes' => ['required', 'integer', 'min:0'],
            'is_preview' => ['boolean'],
        ]);

        if ($request->hasFile('content_file')) {
            $data['content_path'] = $request->file('content_file')->store('courses/lessons', 'public');
        }

        $data['sort_order'] = $section->lessons()->max('sort_order') + 1;
        $data['course_id'] = $course->id;

        $section->lessons()->create($data);

        $course->increment('lessons_count');
        $course->update(['duration_minutes' => $course->lessons()->sum('duration_minutes')]);

        return back()->with('status', 'Lesson added.');
    }

    public function destroyLesson(Course $course, CourseLesson $lesson): RedirectResponse
    {
        $this->authorize('manageCurriculum', $course);

        $lesson->delete();
        $course->decrement('lessons_count');
        $course->update(['duration_minutes' => $course->lessons()->sum('duration_minutes')]);

        return back()->with('status', 'Lesson removed.');
    }

    public function destroySection(Course $course, CourseSection $section): RedirectResponse
    {
        $this->authorize('manageCurriculum', $course);

        $section->delete();

        return back()->with('status', 'Section removed.');
    }
}
