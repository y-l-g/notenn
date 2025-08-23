<?php

namespace App\Models;

use App\Jobs\UpdateSearchIndex;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Like extends Model
{
    protected $fillable = ['user_id', 'likeable_id', 'type'];

    protected static function booted()
    {
        static::saved(function ($like) {
            UpdateSearchIndex::dispatch();
        });

        static::deleted(function ($like) {
            UpdateSearchIndex::dispatch();
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withDefault();
    }

    public function likeable(): MorphTo
    {
        return $this->morphTo();
    }
}
