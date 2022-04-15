<?php

namespace App\Models\Board;

use App\Models\Board\Card;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Column extends Model
{
    use HasFactory, SoftDeletes;

    public function cards(): HasMany
    {
        return $this->hasMany(Card::class)->orderBy('order', 'ASC');
    }
}
