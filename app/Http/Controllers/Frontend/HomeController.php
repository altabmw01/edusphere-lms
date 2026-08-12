<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Testimonial;
use App\Services\BookService;
use App\Services\CourseService;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct(
        protected CourseService $courseService,
        protected BookService $bookService,
    ) {
    }

    public function index(): View
    {
        return view('frontend.home', [
            'featuredCourses' => $this->courseService->featured(6),
            'featuredBooks' => $this->bookService->featured(6),
            'courseCategories' => Category::type('course')->active()->ordered()->withCount('courses')->limit(12)->get(),
            'testimonials' => Testimonial::active()->latest()->limit(6)->get(),
        ]);
    }

    public function about(): View
    {
        return view('frontend.about');
    }

    public function faq(): View
    {
        return view('frontend.faq', [
            'faqs' => \App\Models\Faq::active()->get()->groupBy('category'),
        ]);
    }
}
