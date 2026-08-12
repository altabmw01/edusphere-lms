<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code', 'type', 'value', 'minimum_purchase', 'maximum_discount',
        'usage_limit', 'per_user_limit', 'used_count', 'applicable_to',
        'expires_at', 'status', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'minimum_purchase' => 'decimal:2',
            'maximum_discount' => 'decimal:2',
            'expires_at' => 'datetime',
            'status' => 'boolean',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function usages(): HasMany
    {
        return $this->hasMany(CouponUsage::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', true)
            ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()));
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function hasReachedLimit(): bool
    {
        return $this->usage_limit !== null && $this->used_count >= $this->usage_limit;
    }

    public function calculateDiscount(float $subtotal): float
    {
        if ($subtotal < $this->minimum_purchase) {
            return 0;
        }

        $discount = $this->type === 'percentage'
            ? $subtotal * ($this->value / 100)
            : $this->value;

        if ($this->maximum_discount) {
            $discount = min($discount, $this->maximum_discount);
        }

        return round(min($discount, $subtotal), 2);
    }
}
