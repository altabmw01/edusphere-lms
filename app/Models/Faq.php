<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    protected $fillable = ['question', 'answer', 'category', 'sort_order', 'status'];

    protected function casts(): array
    {
        return ['status' => 'boolean'];
    }

    public function scopeActive($query)
    {
        return $query->where('status', true)->orderBy('sort_order');
    }
}
