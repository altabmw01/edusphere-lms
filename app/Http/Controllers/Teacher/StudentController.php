<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * A teacher has no direct relationship to courses/books — their students are
 * whoever is enrolled in a Batch assigned to them.
 */
class StudentController extends Controller
{
    public function index(Request $request): View
    {
        $batches = Batch::forTeacher(Auth::id())
            ->with([
                'batchable',
                'enrollments.user',
                'bookPurchases.user',
            ])
            ->latest()
            ->get();

        $filterBatchId = $request->integer('batch_id') ?: null;

        // Flatten every batch's students into one list, tagged with which batch/course they belong to.
        $students = $batches
            ->when($filterBatchId, fn ($c) => $c->where('id', $filterBatchId))
            ->flatMap(function (Batch $batch) {
                $courseStudents = $batch->enrollments->map(fn ($enrollment) => [
                    'user' => $enrollment->user,
                    'batch' => $batch,
                    'joined_at' => $enrollment->created_at,
                    'progress' => $enrollment->progress_percent,
                ]);

                $bookStudents = $batch->bookPurchases->map(fn ($purchase) => [
                    'user' => $purchase->user,
                    'batch' => $batch,
                    'joined_at' => $purchase->created_at,
                    'progress' => null,
                ]);

                return $courseStudents->concat($bookStudents);
            })->sortByDesc('joined_at')->values();

        return view('teacher.students.index', [
            'students' => $students,
            'batches' => $batches,
        ]);
    }
}
