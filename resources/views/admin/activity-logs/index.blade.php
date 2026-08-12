@extends('layouts.app')

@section('title', 'Activity Log')
@section('page-title', 'Activity Log')

@section('content')
<div class="table-brand">
    <table class="table mb-0">
        <thead><tr><th>User</th><th>Action</th><th>Description</th><th>IP</th><th>Date</th></tr></thead>
        <tbody>
            @forelse($logs as $log)
                <tr>
                    <td>{{ $log->user?->name ?? 'System' }}</td>
                    <td><span class="badge bg-brand-light text-primary-brand">{{ str_replace('_', ' ', $log->action) }}</span></td>
                    <td class="small text-muted">{{ $log->description }}</td>
                    <td class="small text-muted">{{ $log->ip_address }}</td>
                    <td class="small text-muted">{{ $log->created_at->diffForHumans() }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted py-5">No activity recorded yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $logs->links() }}</div>
@endsection
