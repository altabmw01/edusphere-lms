<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseLesson extends Model
{
    protected $fillable = [
        'course_id', 'course_section_id', 'title', 'type', 'content_path',
        'content_text', 'duration_minutes', 'is_preview', 'sort_order',
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
}
