<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseResource extends Model
{
    protected $fillable = ['course_lesson_id', 'title', 'file_path'];

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(CourseLesson::class, 'course_lesson_id');
    }
}
