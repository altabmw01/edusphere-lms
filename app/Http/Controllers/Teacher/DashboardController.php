<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Course;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(protected ReportService $reports)
    {
    }

    public function index(Request $request): View
    {
        $teacher = $request->user();

        return view('teacher.dashboard', [
            'stats' => $this->reports->teacherStats($teacher->id),
            'myCourses' => Course::where('teacher_id', $teacher->id)->latest()->limit(6)->get(),
            'pendingReviews' => Review::where('reviewable_type', Course::class)
                ->whereIn('reviewable_id', Course::where('teacher_id', $teacher->id)->pluck('id'))
                ->pending()
                ->with('user', 'reviewable')
                ->latest()
                ->limit(5)
                ->get(),
        ]);
    }
}
