@extends('layouts.app')

@section('title', 'My Courses')
@section('page-title', 'My Courses')

@section('content')
<div class="d-flex justify-content-end mb-4">
    <a href="{{ route('teacher.courses.create') }}" class="btn btn-brand"><i class="bi bi-plus"></i> New Course</a>
</div>

<div class="table-brand">
    <table class="table mb-0">
        <thead><tr><th>Course</th><th>Price</th><th>Students</th><th>Rating</th><th>Status</th><th></th></tr></thead>
        <tbody>
            @forelse($courses as $course)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ $course->thumbnail_url }}" width="48" height="36" class="rounded-2" style="object-fit:cover;" alt="{{ $course->title }}">
                            <span>{{ \Illuminate\Support\Str::limit($course->title, 40) }}</span>
                        </div>
                    </td>
                    <td>{{ money($course->final_price) }}</td>
                    <td>{{ number_format($course->students_count) }}</td>
                    <td><span class="rating-stars small">{!! star_rating((float) $course->rating_avg) !!}</span></td>
                    <td><x-status-badge :status="$course->status" /></td>
                    <td class="text-end">
                        <a href="{{ route('teacher.courses.curriculum.edit', $course) }}" class="btn btn-icon-circle" title="Curriculum"><i class="bi bi-list-check"></i></a>
                        <a href="{{ route('teacher.courses.edit', $course) }}" class="btn btn-icon-circle"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('teacher.courses.destroy', $course) }}" method="POST" class="d-inline" data-confirm="Delete this course?">
                            @csrf @method('DELETE')
                            <button class="btn btn-icon-circle text-danger"><i class="bi bi-trash3"></i></button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-5">You haven't created any courses yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $courses->links() }}</div>
@endsection
