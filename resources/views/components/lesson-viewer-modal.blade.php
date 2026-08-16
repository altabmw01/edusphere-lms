@props(['lesson'])

<div class="modal fade" id="lessonModal{{ $lesson->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius: var(--radius-lg);">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title">{{ $lesson->title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-2">
                @if($lesson->type === 'video' && $lesson->embed_url)
                    <div class="ratio ratio-16x9 rounded-3 overflow-hidden mb-3 bg-dark">
                        {{-- src is intentionally left empty here — set only when the modal opens,
                             so the video doesn't preload/autoplay for every lesson on page load. --}}
                        <iframe data-lazy-src="{{ $lesson->embed_url }}" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>
                    </div>
                    <a href="{{ $lesson->video_url }}" target="_blank" rel="noopener" class="btn btn-outline-brand btn-sm-pill">
                        <i class="bi bi-box-arrow-up-right me-1"></i> Watch on {{ $lesson->video_platform }}
                    </a>

                @elseif($lesson->type === 'pdf' && $lesson->content_path)
                    <div class="rounded-3 overflow-hidden mb-3 border" style="height: 60vh;">
                        <iframe data-lazy-src="https://docs.google.com/viewer?url={{ route('lessons.view', $lesson) }}&embedded=true" 
                                style="width:100%; height:100%; border:0;" 
                                title="{{ $lesson->title }}"></iframe>
                    </div>
                    <a href="{{ route('lessons.download', $lesson) }}" class="btn btn-brand btn-sm-pill">
                        <i class="bi bi-download me-1"></i> Download PDF
                    </a>


                @elseif($lesson->type === 'text' || $lesson->type === 'quiz')
                    <div class="lesson-text-content" style="white-space: pre-line; max-height: 60vh; overflow-y: auto;">
                        {{ $lesson->content_text ?: 'No content added for this lesson yet.' }}
                    </div>

                @else
                    <p class="text-muted mb-0">No content has been added for this lesson yet.</p>
                @endif
            </div>
        </div>
    </div>
</div>
