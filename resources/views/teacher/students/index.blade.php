@extends('layouts.app')

@section('title', 'My Students')
@section('page-title', 'My Students')

@section('content')
<form method="GET" class="d-flex gap-2 mb-4">
    <select name="batch_id" class="form-select form-control-custom" style="width:auto;" onchange="this.form.submit()">
        <option value="">All Batches</option>
        @foreach($batches as $batch)
            <option value="{{ $batch->id }}" @selected((string) request('batch_id') === (string) $batch->id)>{{ $batch->batch_name }}</option>
        @endforeach
    </select>
</form>

<div class="table-brand">
    <table class="table mb-0">
        <thead><tr><th>Student</th><th>Batch</th><th>Course/Book</th><th>Progress</th><th>Joined</th></tr></thead>
        <tbody>
            @forelse($students as $row)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ $row['user']->avatarUrl() }}" class="avatar-sm" alt="{{ $row['user']->name }}">
                            <div><span class="d-block small fw-semibold">{{ $row['user']->name }}</span><span class="text-muted small">{{ $row['user']->email }}</span></div>
                        </div>
                    </td>
                    <td>{{ $row['batch']->batch_name }}</td>
                    <td>{{ \Illuminate\Support\Str::limit($row['batch']->batchable?->title, 30) }}</td>
                    <td>
                        @if($row['progress'] !== null)
                            <div class="progress progress-thin" style="width:120px;"><div class="progress-bar" style="width:{{ $row['progress'] }}%;"></div></div>
                            <small class="text-muted">{{ $row['progress'] }}%</small>
                        @else
                            <span class="text-muted small">&mdash;</span>
                        @endif
                    </td>
                    <td class="text-muted small">{{ $row['joined_at']->format('M d, Y') }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted py-5">No students assigned to your batches yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
