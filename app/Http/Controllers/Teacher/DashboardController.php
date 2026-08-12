<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\BatchClass;
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
            'myBatches' => Batch::forTeacher($teacher->id)->with('batchable')->withCount('classes')->latest()->limit(6)->get(),
            'todaysClasses' => BatchClass::whereIn('batch_id', Batch::forTeacher($teacher->id)->pluck('id'))
                ->whereDate('class_start_time', now()->toDateString())
                ->where('status', true)
                ->with('batch.batchable')
                ->orderBy('class_start_time')
                ->get(),
        ]);
    }
}
