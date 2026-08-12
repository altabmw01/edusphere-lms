@extends('layouts.app')

@section('title', 'Batches')
@section('page-title', 'Batches')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <form method="GET" class="d-flex gap-2">
        <select name="type" class="form-select form-control-custom" onchange="this.form.submit()">
            <option value="">All Types</option>
            <option value="course" @selected(request('type') === 'course')>Courses</option>
            <option value="book" @selected(request('type') === 'book')>Books</option>
        </select>
        <select name="teacher_id" class="form-select form-control-custom" onchange="this.form.submit()">
            <option value="">All Teachers</option>
            @foreach($teachers as $teacher)
                <option value="{{ $teacher->id }}" @selected((string) request('teacher_id') === (string) $teacher->id)>{{ $teacher->name }}</option>
            @endforeach
        </select>
    </form>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.batch-levels.index') }}" class="btn btn-outline-brand"><i class="bi bi-bar-chart-steps"></i> Batch Levels</a>
        <a href="{{ route('admin.batches.create') }}" class="btn btn-brand"><i class="bi bi-plus"></i> New Batch</a>
    </div>
</div>

<div class="table-brand">
    <table class="table mb-0">
        <thead><tr><th>Batch</th><th>Course/Book</th><th>Teacher</th><th>Schedule</th><th>Students</th><th>Status</th><th></th></tr></thead>
        <tbody>
            @forelse($batches as $batch)
                <tr>
                    <td>{{ $batch->batch_name }}<br><small class="text-muted">#{{ $batch->batch_number }}</small></td>
                    <td>
                        <span class="badge bg-brand-light text-primary-brand mb-1">{{ $batch->batchable_type === \App\Models\Book::class ? 'Book' : 'Course' }}</span><br>
                        {{ \Illuminate\Support\Str::limit($batch->batchable?->title, 30) }}
                    </td>
                    <td>{{ $batch->teacher?->name }}</td>
                    <td class="small text-muted">
                        {{ implode(', ', $batch->batch_days ?? []) }}<br>
                        {{ $batch->class_start_time?->format('g:i A') }} - {{ $batch->class_end_time?->format('g:i A') }}
                    </td>
                    <td>{{ $batch->enrolled_count }} / {{ $batch->student_limit }}</td>
                    <td><x-status-badge :status="$batch->status ? 'active' : 'inactive'" /></td>
                    <td class="text-end">
                        <a href="{{ route('admin.batches.assign-students', $batch) }}" class="btn btn-icon-circle" title="Assign Students"><i class="bi bi-person-plus"></i></a>
                        <a href="{{ route('admin.batches.edit', $batch) }}" class="btn btn-icon-circle"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('admin.batches.destroy', $batch) }}" method="POST" class="d-inline" data-confirm="Delete this batch?">
                            @csrf @method('DELETE')
                            <button class="btn btn-icon-circle text-danger"><i class="bi bi-trash3"></i></button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted py-5">No batches created yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $batches->links() }}</div>
@endsection
