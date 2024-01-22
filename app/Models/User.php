<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'name',
        'email',
        'password',
        'status',
        'bio',
        'followers_count',
        'following_count',
        'is_admin',
        'blocked',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function recipes(): HasMany
    {
        return $this->hasMany(Recipe::class);
    }

    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'followers_following', 'user_id', 'follow_id');
    }

    public function following(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'followers_following', 'follow_id', 'user_id');
    }

    public function likes(): BelongsToMany
    {
        return $this->belongsToMany(Recipe::class,'recipe_like','liked_by','recipe_id');
    }

    public function rating(): HasMany
    {
        return $this->hasMany(Rating::class,'rated_by');
    }

    public function activity(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    //clculate and update followers count
    public function followersCount(): void
    {
        $followersCount = $this->followers()->count();
        $this->update(['followers_count' => $followersCount]);
    }

    //calculate and update following count
    public function followingCount(): void
    {
        $followingCount = $this->following()->count();
        $this->update(['following_count' => $followingCount]);
    }
    
}
