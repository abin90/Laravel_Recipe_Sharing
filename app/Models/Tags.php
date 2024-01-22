<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tags extends Model
{
    use HasFactory;
    protected $table = 'tags';
    protected $fillable = [
        'tag_name',
        'recipe_id',
    ];

    public function recipes(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }
}
