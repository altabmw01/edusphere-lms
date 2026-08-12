<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title', 'slug', 'category_id', 'created_by', 'thumbnail', 'banner',
        'price', 'discount_price', 'level', 'language', 'duration_minutes',
        'lessons_count', 'has_certificate', 'description', 'requirements',
        'target_audience', 'what_you_will_learn', 'status', 'is_featured',
        'is_trending', 'meta_title', 'meta_description', 'published_at',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'discount_price' => 'decimal:2',
            'has_certificate' => 'boolean',
            'is_featured' => 'boolean',
            'is_trending' => 'boolean',
            'rating_avg' => 'decimal:2',
            'published_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Course $course) {
            $course->slug = $course->slug ?: Str::slug($course->title) . '-' . Str::random(5);
        });
    }

    // --- Relationships ---
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function sections(): HasMany
    {
        return $this->hasMany(CourseSection::class)->orderBy('sort_order');
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(CourseLesson::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(CourseEnrollment::class);
    }

    public function batches(): MorphMany
    {
        return $this->morphMany(Batch::class, 'batchable');
    }

    public function activeBatches(): MorphMany
    {
        return $this->batches()->active()->visible();
    }

    public function reviews(): MorphMany
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    public function approvedReviews(): MorphMany
    {
        return $this->reviews()->where('status', 'approved');
    }

    public function wishlistedBy(): MorphMany
    {
        return $this->morphMany(Wishlist::class, 'wishlistable');
    }

    // --- Scopes ---
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeTrending($query)
    {
        return $query->where('is_trending', true);
    }

    public function scopeOfCategory($query, int $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeSearch($query, ?string $term)
    {
        return $term
            ? $query->where('title', 'like', "%{$term}%")
            : $query;
    }

    // --- Accessors ---
    public function getFinalPriceAttribute(): float
    {
        return (float) ($this->discount_price ?? $this->price);
    }

    public function getDiscountPercentAttribute(): int
    {
        if (! $this->discount_price || $this->price <= 0) {
            return 0;
        }

        return (int) round((($this->price - $this->discount_price) / $this->price) * 100);
    }

    public function getThumbnailUrlAttribute(): string
    {
        return $this->thumbnail
            ? asset('storage/' . $this->thumbnail)
            : 'https://picsum.photos/seed/course' . $this->id . '/500/320';
    }

    public function getDurationHoursAttribute(): float
    {
        return round($this->duration_minutes / 60, 1);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
