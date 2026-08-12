@extends('layouts.app')

@section('title', $batch->batch_name)
@section('page-title', 'Manage Classes')

@section('content')
<div class="filter-card mb-4">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
        <div>
            <h5 class="mb-1">{{ $batch->batch_name }} <span class="text-muted small fw-normal">#{{ $batch->batch_number }}</span></h5>
            <p class="small text-muted mb-2">{{ \Illuminate\Support\Str::limit($batch->batchable?->title, 60) }} &middot; {{ $batch->batchLevel?->name ?? 'No level' }}</p>
            <p class="small text-muted mb-0">
                <i class="bi bi-calendar-week me-1"></i> {{ implode(', ', $batch->batch_days ?? []) }}
                &nbsp;&middot;&nbsp;
                <i class="bi bi-clock me-1"></i> {{ $batch->class_start_time?->format('g:i A') }} - {{ $batch->class_end_time?->format('g:i A') }}
                &nbsp;&middot;&nbsp;
                <i class="bi bi-people me-1"></i> {{ $batch->enrolled_count }} / {{ $batch->student_limit }} students
            </p>
        </div>
        <button class="btn btn-brand" data-bs-toggle="modal" data-bs-target="#addClassModal"><i class="bi bi-plus"></i> Add Class Link</button>
    </div>
</div>

<div class="table-brand">
    <table class="table mb-0">
        <thead><tr><th>Date</th><th>Type</th><th>Link</th><th>Code / Pass</th><th>Status</th><th></th></tr></thead>
        <tbody>
            @forelse($batch->classes as $class)
                <tr>
                    <td>
                        {{ $class->class_start_time->format('M d, Y') }}<br>
                        <small class="text-muted">{{ $class->class_start_time->format('g:i A') }} - {{ $class->class_end_time->format('g:i A') }}</small>
                    </td>
                    <td>{{ $class->linkType?->name }}</td>
                    <td><a href="{{ $class->full_link }}" target="_blank" class="small">Open Link <i class="bi bi-box-arrow-up-right"></i></a></td>
                    <td class="small text-muted">
                        @if($class->metting_code)Code: {{ $class->metting_code }}<br>@endif
                        @if($class->metting_pass_code)Pass: {{ $class->metting_pass_code }}@endif
                    </td>
                    <td><x-status-badge :status="$class->status ? 'active' : 'inactive'" /></td>
                    <td class="text-end">
                        <button class="btn btn-icon-circle" data-bs-toggle="modal" data-bs-target="#editClassModal{{ $class->id }}"><i class="bi bi-pencil"></i></button>
                        <form action="{{ route('teacher.classes.destroy', $class) }}" method="POST" class="d-inline" data-confirm="Delete this class link?">
                            @csrf @method('DELETE')
                            <button class="btn btn-icon-circle text-danger"><i class="bi bi-trash3"></i></button>
                        </form>
                    </td>
                </tr>

                <div class="modal fade" id="editClassModal{{ $class->id }}" tabindex="-1">
                    <div class="modal-dialog modal-lg"><div class="modal-content" style="border-radius:var(--radius-lg);">
                        <div class="modal-body p-4">
                            <h5 class="mb-3">Edit Class Link</h5>
                            <form method="POST" action="{{ route('teacher.classes.update', $class) }}">
                                @csrf @method('PUT')
                                @include('partials.forms.batch-class-fields', ['class' => $class])
                                <button class="btn btn-brand w-100 mt-2">Save Changes</button>
                            </form>
                        </div>
                    </div></div>
                </div>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-5">No class links added yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="modal fade" id="addClassModal" tabindex="-1">
    <div class="modal-dialog modal-lg"><div class="modal-content" style="border-radius:var(--radius-lg);">
        <div class="modal-body p-4">
            <h5 class="mb-3">Add Class Link</h5>
            <form method="POST" action="{{ route('teacher.batches.classes.store', $batch) }}">
                @csrf
                @include('partials.forms.batch-class-fields')
                <button class="btn btn-brand w-100 mt-2">Add Class Link</button>
            </form>
        </div>
    </div></div>
</div>
@endsection
