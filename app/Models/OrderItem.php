<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class OrderItem extends Model
{
    protected $fillable = ['order_id', 'purchasable_type', 'purchasable_id', 'batch_id', 'title', 'price', 'quantity', 'line_total'];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'line_total' => 'decimal:2',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function purchasable(): MorphTo
    {
        return $this->morphTo();
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }
}
