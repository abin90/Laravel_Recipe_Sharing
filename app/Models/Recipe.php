<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Recipe extends Model
{
    protected $table = 'recipe';
    use HasFactory;

    protected $fillable = [
        'title',
        'ingredients',
        'steps',
        'cooking_time',
        'difficulty_level',
        'rating',
        'likes',
        'tags',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(RecipeImages::class, 'recipe_id');
    }

    public function recipeLikes(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'recipe_like', 'recipe_id', 'liked_by');
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class, 'recipe_id');
    }

    public function tag(): HasMany
    {
        return $this->hasMany(Tag::class, 'recipe_id');
    }

    public function activity(): HasOne
    {
        return $this->hasOne(Activity::class);
    }


    //calculating and updating average rating of racipes
    public function averageRating(): void
    {
        $averageRating = $this->ratings()->avg('rating');
        $this->update(['rating' => $averageRating]);
    }

    //function to update likes
    public function updateLikes($increment = true): void
{
    $likes = $this->likes;

    if ($increment) {
        $likes++;
    } else {
        $likes = max(0, $likes - 1);
    }

    $this->update(['likes' => $likes]);
}

}
