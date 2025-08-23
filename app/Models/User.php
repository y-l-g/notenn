<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Likeable;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;

class User extends Authenticatable implements \Illuminate\Contracts\Auth\MustVerifyEmail, HasLocalePreference
{
    use Billable, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_pro',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_pro' => 'boolean',
        ];
    }

    public function preferredLocale(): ?string
    {
        return $this->locale;
    }

    public function tunebooks(): HasMany
    {
        return $this->hasMany(Tunebook::class);
    }

    public function isPro()
    {
        return $this->is_pro;
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function tunes(): HasMany
    {
        return $this->hasMany(Tune::class);
    }

    public function arrangements(): HasMany
    {
        return $this->hasMany(Arrangement::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }


    public function like(Likeable $likeable): self
    {
        if (
            $likeable->likes()
                ->where('user_id', auth()->id())
                ->exists()
        ) {
            $likeable->likes()->where('user_id', auth()->id())->delete();

            return $this;
        }
        (new Like)
            ->user()->associate($this)
            ->likeable()->associate($likeable)
            ->save();

        return $this;
    }

    public function unlike(Likeable $likeable): self
    {
        if (
            !$likeable->likes()
                ->where('user_id', auth()->id())
                ->exists()
        ) {
            return $this;
        }
        $likeable->likes()->where('user_id', auth()->id())->delete();

        return $this;
    }

}
