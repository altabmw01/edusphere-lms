<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class BatchController extends Controller
{
    use AuthorizesRequests;
    
    public function index(): View
    {
        $batches = Batch::forTeacher(Auth::id())
            ->with('batchable', 'batchLevel')
            ->withCount('classes')
            ->latest()
            ->paginate(12);

        return view('teacher.batches.index', ['batches' => $batches]);
    }

    public function show(Batch $batch): View
    {
        $this->authorize('view', $batch);

        $batch->load(['batchable', 'batchLevel', 'classes' => fn ($q) => $q->latest('class_start_time')]);

        return view('teacher.batches.show', [
            'batch' => $batch,
            'linkTypes' => \App\Models\LinkType::orderBy('name')->get(),
        ]);
    }
}
