<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Course;
use App\Services\CourseService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function __construct(protected CourseService $courseService)
    {
    }

    public function index(Request $request): View
    {
        $courses = $this->courseService->browse($request->only(['search', 'category_id', 'level', 'min_price', 'max_price', 'sort']));

        return view('frontend.courses.index', [
            'courses' => $courses,
            'categories' => Category::type('course')->active()->ordered()->get(),
        ]);
    }

    public function show(string $slug): View
    {
        $course = $this->courseService->show($slug);
        abort_unless($course && $course->status === 'published', 404);

        return view('frontend.courses.show', [
            'course' => $course,
            'related' => $this->courseService->related($course),
            'isEnrolled' => auth()->check() && auth()->user()->hasPurchasedCourse($course->id),
        ]);
    }

    /**
     * Serves a PDF lesson's file as a forced download. Open to guests for free
     * preview lessons; otherwise requires the requester to have purchased the
     * lesson's course. Shared by the public course preview modal and the
     * Continue Learning page.
     */
    public function downloadLesson(\App\Models\CourseLesson $lesson)
    {
        abort_unless($lesson->type === 'pdf' && $lesson->content_path, 404);

        $canAccess = $lesson->is_preview
            || (auth()->check() && auth()->user()->hasPurchasedCourse($lesson->course_id));

        abort_unless($canAccess, 403, 'Purchase this course to download this lesson.');

        abort_unless(\Illuminate\Support\Facades\Storage::disk('public')->exists($lesson->content_path), 404, 'File not available.');

        return \Illuminate\Support\Facades\Storage::disk('public')->download($lesson->content_path, \Illuminate\Support\Str::slug($lesson->title) . '.pdf');
    }

    /**
     * Streams the PDF inline (Content-Disposition: inline) for the modal's
     * <iframe> preview, rather than relying on the public/storage symlink
     * directly — keeps preview working even if storage:link isn't set up,
     * and avoids ambiguous browser behavior around raw static file links.
     */
    public function viewLesson(\App\Models\CourseLesson $lesson)
    {
        abort_unless($lesson->type === 'pdf' && $lesson->content_path, 404);

        $canAccess = $lesson->is_preview
            || (auth()->check() && auth()->user()->hasPurchasedCourse($lesson->course_id));

        abort_unless($canAccess, 403, 'Purchase this course to view this lesson.');

        abort_unless(\Illuminate\Support\Facades\Storage::disk('public')->exists($lesson->content_path), 404, 'File not available.');

        return \Illuminate\Support\Facades\Storage::disk('public')->response($lesson->content_path, \Illuminate\Support\Str::slug($lesson->title) . '.pdf');
    }
}
