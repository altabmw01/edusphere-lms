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
}
