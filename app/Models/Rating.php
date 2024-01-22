<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rating extends Model
{
    use HasFactory;
    protected $table = 'rating';
    protected $fillable = [
        'recipe_id',
        'rated_by',
        'rating',

    ];

    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class, 'recipe_id')->onDelete(function ($recipe) {
            
        });
    }

    public function ratedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rated_by')->onDelete(function ($user) {
            
        });
    }
}
