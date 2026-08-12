<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BatchLevel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BatchLevelController extends Controller
{
    public function index(): View
    {
        return view('admin.batch-levels.index', [
            'batchLevels' => BatchLevel::withCount('batches')->latest()->paginate(20),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
        ]);

        BatchLevel::create($data);

        return back()->with('status', 'Batch level created.');
    }

    public function update(Request $request, BatchLevel $batchLevel): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'status' => ['boolean'],
        ]);

        $batchLevel->update($data);

        return back()->with('status', 'Batch level updated.');
    }

    public function destroy(BatchLevel $batchLevel): RedirectResponse
    {
        if ($batchLevel->batches()->exists()) {
            return back()->with('error', 'This level is used by existing batches and cannot be deleted.');
        }

        $batchLevel->delete();

        return back()->with('status', 'Batch level deleted.');
    }
}
