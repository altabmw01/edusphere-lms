@extends('layouts.app')

@section('title', 'Courses')
@section('page-title', 'Courses')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <form method="GET" class="d-flex gap-2">
        <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-custom" placeholder="Search courses..." style="width:260px;">
        <select name="status" class="form-select form-control-custom" onchange="this.form.submit()">
            <option value="">All Statuses</option>
            <option value="draft" @selected(request('status') === 'draft')>Draft</option>
            <option value="pending" @selected(request('status') === 'pending')>Pending</option>
            <option value="published" @selected(request('status') === 'published')>Published</option>
        </select>
        <button class="btn btn-outline-brand" type="submit">Filter</button>
    </form>
    <a href="{{ route('admin.courses.create') }}" class="btn btn-brand"><i class="bi bi-plus"></i> New Course</a>
</div>

<div class="table-brand">
    <table class="table mb-0">
        <thead><tr><th>Course</th><th>Category</th><th>Price</th><th>Students</th><th>Status</th><th></th></tr></thead>
        <tbody>
            @forelse($courses as $course)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ $course->thumbnail_url }}" width="48" height="36" class="rounded-2" style="object-fit:cover;" alt="{{ $course->title }}">
                            <span>{{ \Illuminate\Support\Str::limit($course->title, 40) }}</span>
                        </div>
                    </td>
                    <td>{{ $course->category?->name }}</td>
                    <td>{{ money($course->final_price) }}</td>
                    <td>{{ number_format($course->students_count) }}</td>
                    <td><x-status-badge :status="$course->status" /></td>
                    <td class="text-end">
                        <a href="{{ route('admin.courses.edit', $course) }}" class="btn btn-icon-circle"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('admin.courses.destroy', $course) }}" method="POST" class="d-inline" data-confirm="Delete this course? This cannot be undone.">
                            @csrf @method('DELETE')
                            <button class="btn btn-icon-circle text-danger"><i class="bi bi-trash3"></i></button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-5">No courses found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $courses->links() }}</div>
@endsection
