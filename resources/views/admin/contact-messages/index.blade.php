@extends('layouts.app')

@section('title', 'Contact Messages')
@section('page-title', 'Contact Messages')

@section('content')
<div class="table-brand">
    <table class="table mb-0">
        <thead><tr><th></th><th>From</th><th>Subject</th><th>Received</th><th></th></tr></thead>
        <tbody>
            @forelse($messages as $message)
                <tr class="{{ $message->is_read ? '' : 'fw-semibold' }}">
                    <td>@if(!$message->is_read)<span class="badge bg-primary">New</span>@endif</td>
                    <td>{{ $message->name }} <span class="text-muted small fw-normal">({{ $message->email }})</span></td>
                    <td>{{ \Illuminate\Support\Str::limit($message->subject, 50) }}</td>
                    <td class="small text-muted fw-normal">{{ $message->created_at->diffForHumans() }}</td>
                    <td class="text-end">
                        <a href="{{ route('admin.messages.show', $message) }}" class="btn btn-icon-circle"><i class="bi bi-eye"></i></a>
                        <form action="{{ route('admin.messages.destroy', $message) }}" method="POST" class="d-inline" data-confirm="Delete this message?">
                            @csrf @method('DELETE')
                            <button class="btn btn-icon-circle text-danger"><i class="bi bi-trash3"></i></button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted py-5">No messages yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $messages->links() }}</div>
@endsection
