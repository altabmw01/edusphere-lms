@php($lesson = $lesson ?? null)
@php($uid = $lesson?->id ?? 'new'.$section->id)

<x-form.input name="title" label="Lesson Title" :value="$lesson?->title" required />

<div class="mb-3">
    <label class="form-label-custom">Lesson Type</label>
    <select name="type" class="form-select form-control-custom lesson-type-select" data-uid="{{ $uid }}" required>
        <option value="video" @selected(($lesson?->type ?? 'video') === 'video')>Video (YouTube / Vimeo)</option>
        <option value="pdf" @selected($lesson?->type === 'pdf')>PDF</option>
        <option value="text" @selected($lesson?->type === 'text')>Text</option>
        <option value="quiz" @selected($lesson?->type === 'quiz')>Quiz</option>
    </select>
</div>

<x-form.input name="duration_minutes" type="number" label="Duration (minutes)" :value="$lesson?->duration_minutes ?? 10" required />

{{-- Video: separate YouTube / Vimeo fields — admin fills in whichever platform applies.
     Accepts either a full link or just the bare video code/ID. --}}
<div class="lesson-field-video" data-uid="{{ $uid }}">
    <x-form.input
        name="youtube_input"
        label="YouTube Video Link or Code"
        :value="$lesson?->video_platform === 'YouTube' ? $lesson?->video_url : null"
        placeholder="https://www.youtube.com/watch?v=... or just the video code"
    />
    <x-form.input
        name="vimeo_input"
        label="Vimeo Video Link or Code"
        :value="$lesson?->video_platform === 'Vimeo' ? $lesson?->video_url : null"
        placeholder="https://vimeo.com/... or just the video code"
        hint="Fill in only one of YouTube or Vimeo, whichever platform this video is on."
    />
</div>

{{-- PDF: file upload --}}
<div class="lesson-field-pdf d-none" data-uid="{{ $uid }}">
    <label class="form-label-custom">Upload PDF File</label>
    @if($lesson?->content_path)
        <p class="small text-success mb-2"><i class="bi bi-file-earmark-pdf"></i> A PDF is already uploaded. Choose a new file only to replace it.</p>
    @endif
    <input type="file" name="content_file" class="form-control form-control-custom mb-3" accept="application/pdf">
</div>

{{-- Text / Quiz: text body --}}
<div class="lesson-field-text d-none" data-uid="{{ $uid }}">
    <x-form.textarea name="content_text" label="Lesson Content" rows="4" :value="$lesson?->content_text" />
</div>

<div class="form-check form-switch mb-3">
    <input class="form-check-input" type="checkbox" name="is_preview" value="1" id="preview{{ $uid }}" @checked($lesson?->is_preview)>
    <label class="form-check-label small" for="preview{{ $uid }}">Free Preview Lesson</label>
</div>

<button class="btn btn-brand w-100">{{ $lesson ? 'Save Changes' : 'Add Lesson' }}</button>
