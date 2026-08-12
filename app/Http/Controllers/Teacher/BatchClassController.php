<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\BatchClass;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class BatchClassController extends Controller
{

    use AuthorizesRequests;

    public function store(Request $request, Batch $batch): RedirectResponse
    {
        $this->authorize('manageClasses', $batch);

        $data = $this->validated($request);
        $data['batch_id'] = $batch->id;
        $data['batchable_type'] = $batch->batchable_type;
        $data['batchable_id'] = $batch->batchable_id;
        $data['teacher_id'] = Auth::id();

        BatchClass::create($data);

        return back()->with('status', 'Class link added.');
    }

    public function update(Request $request, BatchClass $class): RedirectResponse
    {
        $this->authorize('update', $class);

        $class->update($this->validated($request));

        return back()->with('status', 'Class link updated.');
    }

    public function destroy(BatchClass $class): RedirectResponse
    {
        $this->authorize('delete', $class);

        $class->delete();

        return back()->with('status', 'Class link removed.');
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'link_type_id' => ['required', 'exists:link_types,id'],
            'full_link' => ['required', 'url', 'max:500'],
            'metting_code' => ['nullable', 'string', 'max:100'],
            'metting_pass_code' => ['nullable', 'string', 'max:100'],
            'class_start_time' => ['required', 'date'],
            'class_end_time' => ['required', 'date', 'after:class_start_time'],
            'class_note' => ['nullable', 'string', 'max:1000'],
            'status' => ['boolean'],
        ]);
    }
}
