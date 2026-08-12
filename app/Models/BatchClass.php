<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class BatchClass extends Model
{
    use HasFactory;

    protected $table = 'batch_classes';

    protected $fillable = [
        'link_type_id',
        'batchable_type',
        'batchable_id',
        'teacher_id',
        'batch_id',
        'full_link',
        'metting_code',
        'metting_pass_code',
        'class_start_time',
        'class_end_time',
        'class_note',
        'status',
        'notified',
    ];

    protected function casts(): array
    {
        return [
            'class_start_time' => 'datetime',
            'class_end_time' => 'datetime',
            'status' => 'boolean',
            'notified' => 'boolean',
        ];
    }

    public function linkType(): BelongsTo
    {
        return $this->belongsTo(LinkType::class);
    }

    public function batchable(): MorphTo
    {
        return $this->morphTo();
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('status', true)->where('class_start_time', '>=', now());
    }
}
