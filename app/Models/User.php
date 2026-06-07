<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;


#[Fillable(['name', 'email','is_personalized', 'password', 'avatar', 'gender', 'dob', 'bio', 'phone', 'password_changed_at'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function watchlists()
    {
        return $this->hasMany(Watchlist::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function userGenres()
    {
        return $this->hasMany(UserGenre::class, 'user_id', 'id');
    }

    public function taste()
    {
        return $this->hasOne(UserTaste::class, 'user_id', 'id');
    }
}