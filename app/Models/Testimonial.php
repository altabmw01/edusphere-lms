<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    protected $fillable = ['name', 'designation', 'photo', 'rating', 'content', 'status'];

    protected function casts(): array
    {
        return ['status' => 'boolean'];
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }
}
