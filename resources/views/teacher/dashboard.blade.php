@extends('layouts.app')

@section('title', 'Teacher Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-lg-3 col-md-6">
        <div class="stat-card"><div class="stat-icon" style="background:var(--gradient-primary);"><i class="bi bi-calendar3"></i></div><h4 class="mb-0">{{ $stats['total_batches'] }}</h4><small class="text-muted">My Batches</small></div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="stat-card"><div class="stat-icon" style="background:var(--gradient-warm);"><i class="bi bi-people"></i></div><h4 class="mb-0">{{ $stats['total_students'] }}</h4><small class="text-muted">Total Students</small></div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="stat-card"><div class="stat-icon" style="background:#22C55E;"><i class="bi bi-cash-stack"></i></div><h4 class="mb-0">{{ money($stats['revenue']) }}</h4><small class="text-muted">Batch Revenue</small></div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="stat-card"><div class="stat-icon" style="background:#8B5CF6;"><i class="bi bi-camera-video"></i></div><h4 class="mb-0">{{ $stats['total_classes'] }}</h4><small class="text-muted">Class Links Added</small></div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="table-brand">
            <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                <h6 class="mb-0">My Batches</h6>
                <a href="{{ route('teacher.batches.index') }}" class="small text-primary-brand">View all</a>
            </div>
            <table class="table mb-0">
                <thead><tr><th>Batch</th><th>Course/Book</th><th>Schedule</th><th>Class Links</th><th></th></tr></thead>
                <tbody>
                    @forelse($myBatches as $batch)
                        <tr>
                            <td>{{ $batch->batch_name }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($batch->batchable?->title, 30) }}</td>
                            <td class="small text-muted">{{ implode(', ', $batch->batch_days ?? []) }}</td>
                            <td>{{ $batch->classes_count }}</td>
                            <td><a href="{{ route('teacher.batches.show', $batch) }}" class="btn btn-icon-circle"><i class="bi bi-arrow-right"></i></a></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">No batches have been assigned to you yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="table-brand">
            <div class="p-3 border-bottom"><h6 class="mb-0">Today's Classes</h6></div>
            @forelse($todaysClasses as $class)
                <div class="d-flex align-items-center gap-3 p-3 border-bottom">
                    <div class="feature-icon mb-0" style="width:42px;height:42px;font-size:16px;"><i class="bi bi-camera-video"></i></div>
                    <div class="flex-grow-1">
                        <span class="d-block small fw-semibold">{{ \Illuminate\Support\Str::limit($class->batch->batchable?->title, 28) }}</span>
                        <span class="text-muted small">{{ $class->class_start_time->format('g:i A') }} - {{ $class->class_end_time->format('g:i A') }}</span>
                    </div>
                    <a href="{{ route('teacher.batches.show', $class->batch) }}" class="btn btn-icon-circle"><i class="bi bi-arrow-right"></i></a>
                </div>
            @empty
                <p class="text-muted small p-3 mb-0">No classes scheduled for today.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
