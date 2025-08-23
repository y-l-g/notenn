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
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;

class Arrangement extends Model implements Likeable, Taggable
{
    use HasFactory;
    use Likes;
    use Searchable;
    use SoftDeletes;
    use Tags;

    protected $fillable = [
        'tune_id',
        'rhythm_id',
        'name',
        'tune_body_lines',
        'meter_id',
        'tempo',
        'parts',
        'transcription',
        'notes_lines',
        'key',
        'words_lines',
        'user_id',
        'origin_id',
    ];

    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'title' => $this->tune->title,
            'alternative_titles' => $this->tune->alternative_titles,
            'similar_arrangements_with_different_tune' => $this->similar_arrangements_with_different_tune,
            'composer_name' => $this->tune->composer?->name,
            'origin_name_fr' => $this->tune->origin?->name_fr,
            'origin_name_br' => $this->tune->origin?->name_br,
            'origin_name_en' => $this->tune->origin?->name_en,
            'origin_name_es' => $this->tune->origin?->name_es,
            'meter_name' => $this->meter?->name,
            'rhythm_name_fr' => $this->rhythm?->name_fr,
            'rhythm_name_br' => $this->rhythm?->name_br,
            'rhythm_name_en' => $this->rhythm?->name_en,
            'rhythm_name_es' => $this->rhythm?->name_es,
            'composer' => $this->tune->composer?->name,
            'origin' => $this->tune->origin,
            'meter' => $this->meter,
            'rhythm' => $this->rhythm,
            'likes_count' => $this->likes_count,
            'tune_body_lines' => $this->tune_body_lines,
            'notes_lines' => $this->notes_lines,
            'words_lines' => $this->words_lines,
            'key' => $this->key,
            'is_best_arrangement' => $this->is_best_arrangement,
            'created_at' => $this->created_at,
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'tune' => $this->tune,
            'tags' => $this->tags->pluck('name')->toArray(),
            'country' => $this->country,
        ];
    }
    // protected $appends = ['tune_title'];

    public function tune(): BelongsTo
    {
        return $this->belongsTo(Tune::class);
    }


    public function collaborators()
    {
        return $this->belongsToMany(User::class, 'arrangement_collaborators')
            ->withTimestamps();
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tunebooks(): BelongsToMany
    {
        return $this->belongsToMany(Tunebook::class, 'tunebook_arrangement')->withTimestamps();
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function meter(): BelongsTo
    {
        return $this->belongsTo(Meter::class);
    }

    public function rhythm(): BelongsTo
    {
        return $this->belongsTo(Rhythm::class);
    }

    public function isBestArrangement(): Attribute
    {
        return Attribute::make(
            get: fn() => ($this->tune->best_arrangement->id === $this->id),
        );
    }

    public function likesCount(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->likes()->count()
        );
    }

    public function country(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->tune->origin) {
                    return 'undefined';
                }
                if ($this->tune->origin->type === 'continent') {
                    return 'undefined';
                }
                if ($this->tune->origin->type === 'country') {
                    return $this->tune->origin->name;
                }
                if ($this->tune->origin->type === 'region') {
                    return Origin::find($this->tune->origin->parent_id)->name;
                }
            }
        );
    }
}
