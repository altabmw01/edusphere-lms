<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $fillable = [
        'country_code',
        'country_name',
        'shipping_cost',
    ];

    protected function casts(): array
    {
        return ['shipping_cost' => 'decimal:2'];
    }

    public function isBangladesh(): bool
    {
        return strtoupper($this->country_code) === 'BD';
    }
}
