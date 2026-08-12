@extends('layouts.app')

@section('title', 'Assign Students')
@section('page-title', 'Assign Students to '.$batch->batch_name)

@section('content')
<div class="filter-card mb-4">
    <h6 class="fw-bold mb-1">{{ $batch->batch_name }} <span class="text-muted small fw-normal">#{{ $batch->batch_number }}</span></h6>
    <p class="small text-muted mb-0">{{ \Illuminate\Support\Str::limit($batch->batchable?->title, 60) }} &middot; Teacher: {{ $batch->teacher?->name }} &middot; {{ $batch->enrolled_count }} / {{ $batch->student_limit }} seats filled</p>
</div>

<form method="POST" action="{{ route('admin.batches.assign-students.store', $batch) }}">
    @csrf
    <div class="table-brand mb-4">
        <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
            <h6 class="mb-0">Purchasers Not Yet Assigned to Any Batch</h6>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="checkAll" onclick="document.querySelectorAll('.student-check').forEach(c => c.checked = this.checked)">
                <label class="form-check-label small" for="checkAll">Select All</label>
            </div>
        </div>
        <table class="table mb-0">
            <thead><tr><th></th><th>Student</th><th>Email</th><th>Purchased</th></tr></thead>
            <tbody>
                @forelse($unassigned as $item)
                    <tr>
                        <td><input type="checkbox" class="form-check-input student-check" name="enrollment_ids[]" value="{{ $item->id }}"></td>
                        <td>{{ $item->user->name }}</td>
                        <td class="text-muted small">{{ $item->user->email }}</td>
                        <td class="text-muted small">{{ $item->created_at->format('M d, Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-muted py-5">Everyone who purchased this course/book is already assigned to a batch.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <button class="btn btn-brand" type="submit"><i class="bi bi-check2 me-1"></i> Assign Selected Students</button>
    <a href="{{ route('admin.batches.edit', $batch) }}" class="btn btn-outline-brand">Back to Batch</a>
</form>
@endsection
