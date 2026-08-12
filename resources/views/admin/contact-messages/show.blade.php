@extends('layouts.app')

@section('title', 'View Message')
@section('page-title', 'Contact Message')

@section('content')
<div class="filter-card" style="max-width:700px;">
    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <h5 class="mb-1">{{ $message->subject }}</h5>
            <p class="text-muted small mb-0">From {{ $message->name }} &lt;{{ $message->email }}&gt;</p>
        </div>
        <span class="text-muted small">{{ $message->created_at->format('M d, Y H:i') }}</span>
    </div>
    <hr>
    <p>{{ $message->message }}</p>
    <div class="d-flex gap-2 mt-4">
        <a href="mailto:{{ $message->email }}?subject=Re: {{ $message->subject }}" class="btn btn-brand"><i class="bi bi-reply me-1"></i> Reply via Email</a>
        <form action="{{ route('admin.messages.destroy', $message) }}" method="POST" data-confirm="Delete this message?">
            @csrf @method('DELETE')
            <button class="btn btn-outline-danger">Delete</button>
        </form>
    </div>
</div>
@endsection
