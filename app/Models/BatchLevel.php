<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BatchLevel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'status',
    ];

    protected function casts(): array
    {
        return ['status' => 'boolean'];
    }

    public function batches(): HasMany
    {
        return $this->hasMany(Batch::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }
}
