<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LinkType extends Model
{
    protected $fillable = ['name', 'slug'];

    public function batchClasses(): HasMany
    {
        return $this->hasMany(BatchClass::class);
    }
}
