<?php

namespace App\Models;

use App\Likeable;
use App\Taggable;
use App\Traits\Likes;
use App\Traits\Tags;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Laravel\Scout\Searchable;

class Tunebook extends Model implements Likeable, Taggable
{
    use HasFactory;
    use Likes;
    use Searchable;
    use Tags;

    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'user_id' => $this->user_id,
            'user_name' => $this->user->name,
            'created_at' => $this->created_at,
            'updated_at' => $this->created_at,
            'arrangements_count' => $this->arrangements()->count(),
            'tags' => $this->tags->pluck('name')->toArray(),
            'likes_count' => $this->likes_count,

        ];
    }

    protected $fillable = ['name', 'user_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // protected $appends = ['arrangements_count'];

    public function arrangements(): BelongsToMany
    {
        return $this->belongsToMany(Arrangement::class, 'tunebook_arrangement')->withTimestamps();
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function collaborators()
    {
        return $this->belongsToMany(User::class, 'tunebook_collaborators')
            ->withTimestamps();
    }

    public function arrangementsCount(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->arrangements()->count(),
        );
    }

    public function name(): Attribute
    {
        return Attribute::make(
            get: fn($value) => mb_convert_case($value, MB_CASE_TITLE, 'UTF-8'),
            set: fn($value) => mb_strtolower($value)
        );
    }

    public function likesCount(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->likes()->count()
        );
    }
}
