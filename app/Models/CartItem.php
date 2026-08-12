<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class CartItem extends Model
{
    protected $fillable = ['user_id', 'session_id', 'purchasable_type', 'purchasable_id', 'quantity'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function purchasable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getLineTotalAttribute(): float
    {
        return $this->purchasable->final_price * $this->quantity;
    }
}
