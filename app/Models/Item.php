<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Item extends Model
{
    protected $fillable = ['name', 'description', 'icon', 'slot'];

    public function character(): BelongsTo
    {
        return $this->belongsTo(Character::class);
    }
}