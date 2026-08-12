<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Book extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title', 'slug', 'author', 'category_id', 'added_by', 'thumbnail', 'cover',
        'pdf_path', 'description', 'price', 'discount_price', 'pages', 'language',
        'publisher', 'edition', 'isbn', 'is_featured', 'status', 'meta_title', 'meta_description',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'discount_price' => 'decimal:2',
            'is_featured' => 'boolean',
            'rating_avg' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Book $book) {
            $book->slug = $book->slug ?: Str::slug($book->title) . '-' . Str::random(5);
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function addedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(BookPurchase::class);
    }

    public function reviews(): MorphMany
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    public function approvedReviews(): MorphMany
    {
        return $this->reviews()->where('status', 'approved');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeOfCategory($query, int $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeSearch($query, ?string $term)
    {
        return $term ? $query->where('title', 'like', "%{$term}%") : $query;
    }

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

    public function getCoverUrlAttribute(): string
    {
        return $this->cover
            ? asset('storage/' . $this->cover)
            : 'https://picsum.photos/seed/book' . $this->id . '/500/320';
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
