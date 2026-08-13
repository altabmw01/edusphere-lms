<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseLesson extends Model
{
    protected $fillable = [
        'course_id', 'course_section_id', 'title', 'type', 'content_path',
        'video_url', 'content_text', 'duration_minutes', 'is_preview', 'sort_order',
    ];

    protected function casts(): array
    {
        return ['is_preview' => 'boolean'];
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(CourseSection::class, 'course_section_id');
    }

    public function resources(): HasMany
    {
        return $this->hasMany(CourseResource::class);
    }

    public function progress(): HasMany
    {
        return $this->hasMany(LessonProgress::class);
    }

    /** Public URL to the uploaded PDF file (type = pdf), or null. */
    public function getContentUrlAttribute(): ?string
    {
        return $this->content_path ? asset('storage/' . $this->content_path) : null;
    }

    /** 'YouTube' or 'Vimeo' based on video_url, or null. */
    public function getVideoPlatformAttribute(): ?string
    {
        if (! $this->video_url) {
            return null;
        }

        if (str_contains($this->video_url, 'youtu')) {
            return 'YouTube';
        }

        if (str_contains($this->video_url, 'vimeo')) {
            return 'Vimeo';
        }

        return null;
    }

    /** iframe-ready embed URL converted from a normal YouTube/Vimeo watch link. */
    public function getEmbedUrlAttribute(): ?string
    {
        if ($this->type !== 'video' || ! $this->video_url) {
            return null;
        }

        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]{6,})/', $this->video_url, $m)) {
            return "https://www.youtube.com/embed/{$m[1]}";
        }

        if (preg_match('/vimeo\.com\/(?:video\/)?(\d+)/', $this->video_url, $m)) {
            return "https://player.vimeo.com/video/{$m[1]}";
        }

        return null;
    }
}
