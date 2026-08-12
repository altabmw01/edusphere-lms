<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ClassController extends Controller
{
    public function showCourseClass(Course $course): View
    {
        $enrollment = Auth::user()->enrollments()->where('course_id', $course->id)->firstOrFail();

        abort_if(! $enrollment->batch_id, 404, 'You have not been assigned to a batch for this course yet. Please contact support.');

        $enrollment->load('batch.classes');

        return view('student.class.show', [
            'title' => $course->title,
            'backRoute' => route('student.my-courses.show', $course->slug),
            'batch' => $enrollment->batch,
        ]);
    }

    public function showBookClass(Book $book): View
    {
        $purchase = Auth::user()->bookPurchases()->where('book_id', $book->id)->firstOrFail();

        abort_if(! $purchase->batch_id, 404, 'You have not been assigned to a batch for this book yet. Please contact support.');

        $purchase->load('batch.classes');

        return view('student.class.show', [
            'title' => $book->title,
            'backRoute' => route('books.show', $book->slug),
            'batch' => $purchase->batch,
        ]);
    }
}
