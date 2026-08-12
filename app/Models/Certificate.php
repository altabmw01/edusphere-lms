<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Certificate extends Model
{
    protected $fillable = ['certificate_number', 'user_id', 'course_id', 'file_path', 'issued_at'];

    protected function casts(): array
    {
        return ['issued_at' => 'datetime'];
    }

    protected static function booted(): void
    {
        static::creating(function (Certificate $certificate) {
            $certificate->certificate_number = $certificate->certificate_number
                ?: 'CERT-' . strtoupper(Str::random(10));
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
