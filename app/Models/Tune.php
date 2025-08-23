<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;

class Tune extends Model
{
    /** @use HasFactory<\Database\Factories\TuneFactory> */
    use HasFactory;

    use Searchable;
    use SoftDeletes;

    protected $fillable = [
        'title',
        'composer_id',
        'origin_id',
        'user_id',
    ];

    // protected $appends = [
    //     'best_arrangement'
    // ];

    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'alternative_titles' => $this->alternative_titles,
            'created_at' => $this->created_at,
        ];
    }

    public function composer(): BelongsTo
    {
        return $this->belongsTo(Composer::class);
    }

    public function arrangements(): HasMany
    {
        return $this->hasMany(Arrangement::class);
    }

    public function bestArrangement(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->arrangements()
                ->with('tune')
                ->withCount('likes')
                ->orderByDesc('likes_count')
                ->orderBy('created_at')
                ->first()
        );
    }

    // public function bestArrangement()
    // {
    //     return $this->hasOne(Arrangement::class)->ofMany([
    //         'likes_count' => 'max',
    //         'created_at' => 'max'
    //     ], function ($query) {
    //         $query->withCount('likes');
    //     });
    // }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function origin()
    {
        return $this->belongsTo(Origin::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function title(): Attribute
    {
        return Attribute::make(
            get: fn($value) => mb_convert_case($value, MB_CASE_TITLE, 'UTF-8'),
            set: fn($value) => mb_strtolower($value)
        );
    }
}
