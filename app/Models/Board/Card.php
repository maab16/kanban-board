<?php

namespace App\Models\Board;

use App\Models\Board\Column;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Card extends Model
{
    use HasFactory, SoftDeletes;

    public function column(): BelongsTo
    {
        return $this->belongsTo(Column::class);
    }
}
