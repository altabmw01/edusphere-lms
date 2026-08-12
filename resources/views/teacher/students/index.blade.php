@extends('layouts.app')

@section('title', 'My Students')
@section('page-title', 'My Students')

@section('content')
<div class="table-brand">
    <table class="table mb-0">
        <thead><tr><th>Student</th><th>Course</th><th>Progress</th><th>Enrolled</th></tr></thead>
        <tbody>
            @forelse($enrollments as $enrollment)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ $enrollment->user->avatarUrl() }}" class="avatar-sm" alt="{{ $enrollment->user->name }}">
                            <span>{{ $enrollment->user->name }}</span>
                        </div>
                    </td>
                    <td>{{ \Illuminate\Support\Str::limit($enrollment->course->title, 40) }}</td>
                    <td>
                        <div class="progress progress-thin" style="width:120px;"><div class="progress-bar" style="width:{{ $enrollment->progress_percent }}%;"></div></div>
                        <small class="text-muted">{{ $enrollment->progress_percent }}%</small>
                    </td>
                    <td class="text-muted small">{{ $enrollment->created_at->format('M d, Y') }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-center text-muted py-5">No students enrolled yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $enrollments->links() }}</div>
@endsection
