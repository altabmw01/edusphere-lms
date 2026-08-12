<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseEnrollment;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(): View
    {
        $courseIds = Course::where('teacher_id', auth()->id())->pluck('id');

        $enrollments = CourseEnrollment::whereIn('course_id', $courseIds)
            ->with('user', 'course')
            ->latest()
            ->paginate(20);

        return view('teacher.students.index', ['enrollments' => $enrollments]);
    }
}
