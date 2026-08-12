@extends('layouts.app')

@section('title', 'Batch Levels')
@section('page-title', 'Batch Levels')

@section('content')
<div class="d-flex justify-content-end mb-4">
    <button class="btn btn-brand" data-bs-toggle="modal" data-bs-target="#createLevelModal"><i class="bi bi-plus"></i> New Level</button>
</div>

<div class="table-brand">
    <table class="table mb-0">
        <thead><tr><th>Name</th><th>Batches</th><th>Status</th><th></th></tr></thead>
        <tbody>
            @forelse($batchLevels as $level)
                <tr>
                    <td>{{ $level->name }}</td>
                    <td>{{ $level->batches_count }}</td>
                    <td><x-status-badge :status="$level->status ? 'active' : 'inactive'" /></td>
                    <td class="text-end">
                        <button class="btn btn-icon-circle" data-bs-toggle="modal" data-bs-target="#editLevelModal{{ $level->id }}"><i class="bi bi-pencil"></i></button>
                        <form action="{{ route('admin.batch-levels.destroy', $level) }}" method="POST" class="d-inline" data-confirm="Delete this level?">
                            @csrf @method('DELETE')
                            <button class="btn btn-icon-circle text-danger"><i class="bi bi-trash3"></i></button>
                        </form>
                    </td>
                </tr>

                <div class="modal fade" id="editLevelModal{{ $level->id }}" tabindex="-1">
                    <div class="modal-dialog"><div class="modal-content" style="border-radius:var(--radius-lg);">
                        <div class="modal-body p-4">
                            <h5 class="mb-3">Edit Level</h5>
                            <form method="POST" action="{{ route('admin.batch-levels.update', $level) }}">
                                @csrf @method('PUT')
                                <x-form.input name="name" label="Name" :value="$level->name" required />
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="status" value="1" id="lstatus{{ $level->id }}" @checked($level->status)>
                                    <label class="form-check-label small" for="lstatus{{ $level->id }}">Active</label>
                                </div>
                                <button class="btn btn-brand w-100">Save Changes</button>
                            </form>
                        </div>
                    </div></div>
                </div>
            @empty
                <tr><td colspan="4" class="text-center text-muted py-5">No batch levels yet — e.g. Level 1, Level 2, Advanced Level.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $batchLevels->links() }}</div>

<div class="modal fade" id="createLevelModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content" style="border-radius:var(--radius-lg);">
        <div class="modal-body p-4">
            <h5 class="mb-3">New Batch Level</h5>
            <form method="POST" action="{{ route('admin.batch-levels.store') }}">
                @csrf
                <x-form.input name="name" label="Name" hint="e.g. Level 1, Level 2, Advanced Level" required />
                <button class="btn btn-brand w-100">Create Level</button>
            </form>
        </div>
    </div></div>
</div>
@endsection
