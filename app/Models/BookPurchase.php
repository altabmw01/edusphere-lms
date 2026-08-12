<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookPurchase extends Model
{
    protected $fillable = ['user_id', 'book_id', 'order_id', 'download_count', 'last_downloaded_at'];

    protected function casts(): array
    {
        return ['last_downloaded_at' => 'datetime'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }
	
	public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
