<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'type', 'icon', 'color', 'description', 'sort_order', 'status',
    ];

    protected function casts(): array
    {
        return ['status' => 'boolean'];
    }

    protected static function booted(): void
    {
        static::creating(function (Category $category) {
            $category->slug = $category->slug ?: Str::slug($category->name);
        });
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }

    public function scopeType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
