<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'headline', 'biography', 'social_links', 'skills',
        'experience_years', 'rating_avg', 'rating_count', 'total_revenue',
    ];

    protected function casts(): array
    {
        return [
            'social_links' => 'array',
            'skills' => 'array',
            'rating_avg' => 'decimal:2',
            'total_revenue' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
