@extends('layouts.app')

@section('title', 'My Batches')
@section('page-title', 'My Batches')

@section('content')
<div class="row g-4">
    @forelse($batches as $batch)
        <div class="col-lg-4 col-md-6">
            <div class="filter-card h-100">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="badge bg-brand-light text-primary-brand">{{ $batch->batchable_type === \App\Models\Book::class ? 'Book' : 'Course' }}</span>
                    <x-status-badge :status="$batch->status ? 'active' : 'inactive'" />
                </div>
                <h6 class="mb-1">{{ $batch->batch_name }}</h6>
                <p class="small text-muted mb-2">#{{ $batch->batch_number }} &middot; {{ $batch->batchLevel?->name ?? 'No level' }}</p>
                <p class="small text-muted mb-2">{{ \Illuminate\Support\Str::limit($batch->batchable?->title, 40) }}</p>
                <p class="small text-muted mb-3">
                    <i class="bi bi-calendar-week me-1"></i> {{ implode(', ', $batch->batch_days ?? []) }}<br>
                    <i class="bi bi-clock me-1"></i> {{ $batch->class_start_time?->format('g:i A') }} - {{ $batch->class_end_time?->format('g:i A') }}
                </p>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="small text-muted">{{ $batch->classes_count }} class link(s)</span>
                    <a href="{{ route('teacher.batches.show', $batch) }}" class="btn btn-brand btn-sm-pill">Manage Classes</a>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="empty-state"><i class="bi bi-calendar3"></i><p>No batches have been assigned to you yet.</p></div>
        </div>
    @endforelse
</div>
<div class="mt-4">{{ $batches->links() }}</div>
@endsection
