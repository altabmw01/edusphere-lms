<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Union extends Model
{
    protected $fillable = [
        'thana_id',
        'name',
        'bn_name',
    ];

    public function thana(): BelongsTo
    {
        return $this->belongsTo(Thana::class);
    }
}
