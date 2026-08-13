@extends('layouts.app')

@section('title', 'Curriculum: '.$course->title)
@section('page-title', 'Curriculum Builder')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="mb-0">{{ $course->title }}</h5>
        <span class="text-muted small">{{ $course->sections->count() }} sections &middot; {{ $course->lessons_count }} lessons &middot; {{ duration_for_humans($course->duration_minutes) }} total</span>
    </div>
    <a href="{{ route('admin.courses.edit', $course) }}" class="btn btn-outline-brand">Back to Course</a>
</div>

<div class="accordion mb-4" id="curriculumBuilder">
    @forelse($course->sections as $section)
        <div class="card-brand mb-3 overflow-hidden">
            <div class="d-flex justify-content-between align-items-center p-3 border-bottom" style="background: var(--bg);">
                <button class="btn btn-link text-decoration-none fw-semibold p-0" type="button" data-bs-toggle="collapse" data-bs-target="#sec{{ $section->id }}">
                    {{ $section->title }} <span class="text-muted small fw-normal">({{ $section->lessons->count() }} lessons)</span>
                </button>
                <form action="{{ route('admin.courses.curriculum.sections.destroy', [$course, $section]) }}" method="POST" data-confirm="Delete this section and all its lessons?">
                    @csrf @method('DELETE')
                    <button class="btn btn-icon-circle text-danger btn-sm"><i class="bi bi-trash3"></i></button>
                </form>
            </div>
            <div id="sec{{ $section->id }}" class="collapse show">
                <div class="p-3">
                    @foreach($section->lessons as $lesson)
                        <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                            <span>
                                <i class="bi bi-{{ match($lesson->type) { 'video' => 'play-circle', 'text' => 'file-text', 'pdf' => 'file-earmark-pdf', 'quiz' => 'patch-question', default => 'circle' } }} text-primary-brand me-2"></i>
                                {{ $lesson->title }}
                                <span class="badge bg-brand-light text-primary-brand ms-2">{{ ucfirst($lesson->type) }}</span>
                                @if($lesson->type === 'video' && $lesson->video_platform)<span class="badge bg-secondary ms-1">{{ $lesson->video_platform }}</span>@endif
                                @if($lesson->is_preview)<span class="badge bg-warning text-dark ms-1">Preview</span>@endif
                            </span>
                            <div class="d-flex align-items-center gap-2">
                                <span class="text-muted small">{{ $lesson->duration_minutes }}m</span>
                                <button class="btn btn-icon-circle btn-sm" data-bs-toggle="modal" data-bs-target="#editLessonModal{{ $lesson->id }}"><i class="bi bi-pencil"></i></button>
                                <form action="{{ route('admin.courses.curriculum.lessons.destroy', [$course, $lesson]) }}" method="POST" data-confirm="Remove this lesson?">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-icon-circle btn-sm text-danger"><i class="bi bi-x"></i></button>
                                </form>
                            </div>
                        </div>

                        <div class="modal fade" id="editLessonModal{{ $lesson->id }}" tabindex="-1">
                            <div class="modal-dialog"><div class="modal-content" style="border-radius:var(--radius-lg);">
                                <div class="modal-body p-4">
                                    <h6 class="mb-3">Edit Lesson</h6>
                                    <form action="{{ route('admin.courses.curriculum.lessons.update', [$course, $lesson]) }}" method="POST" enctype="multipart/form-data">
                                        @csrf @method('PUT')
                                        @include('partials.forms.lesson-fields', ['lesson' => $lesson, 'section' => $section])
                                    </form>
                                </div>
                            </div></div>
                        </div>
                    @endforeach

                    <button class="btn btn-outline-brand btn-sm-pill mt-3" type="button" data-bs-toggle="modal" data-bs-target="#addLessonModal{{ $section->id }}"><i class="bi bi-plus"></i> Add Lesson</button>
                </div>
            </div>
        </div>

        <div class="modal fade" id="addLessonModal{{ $section->id }}" tabindex="-1">
            <div class="modal-dialog"><div class="modal-content" style="border-radius:var(--radius-lg);">
                <div class="modal-body p-4">
                    <h6 class="mb-3">Add Lesson to "{{ $section->title }}"</h6>
                    <form action="{{ route('admin.courses.curriculum.lessons.store', [$course, $section]) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @include('partials.forms.lesson-fields', ['section' => $section])
                    </form>
                </div>
            </div></div>
        </div>
    @empty
        <div class="empty-state"><i class="bi bi-list-check"></i><p>No sections yet — add your first section below.</p></div>
    @endforelse
</div>

<div class="filter-card mb-0" style="max-width:500px;">
    <h6 class="fw-bold mb-3">Add New Section</h6>
    <form action="{{ route('admin.courses.curriculum.sections.store', $course) }}" method="POST" class="d-flex gap-2">
        @csrf
        <input type="text" name="title" class="form-control form-control-custom" placeholder="e.g. Module 4: Advanced Topics" required>
        <button class="btn btn-brand">Add Section</button>
    </form>
</div>
@endsection

@push('scripts')
<script>
function toggleLessonFields(select) {
    var uid = select.dataset.uid;
    var type = select.value;
    document.querySelectorAll('.lesson-field-video[data-uid="' + uid + '"]').forEach(el => el.classList.toggle('d-none', type !== 'video'));
    document.querySelectorAll('.lesson-field-pdf[data-uid="' + uid + '"]').forEach(el => el.classList.toggle('d-none', type !== 'pdf'));
    document.querySelectorAll('.lesson-field-text[data-uid="' + uid + '"]').forEach(el => el.classList.toggle('d-none', type !== 'text' && type !== 'quiz'));
}

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.lesson-type-select').forEach(function (select) {
        toggleLessonFields(select);
        select.addEventListener('change', function () { toggleLessonFields(this); });
    });
});
</script>
@endpush
