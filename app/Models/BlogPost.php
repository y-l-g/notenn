<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BlogPost extends Model
{
    protected $fillable = [
        'slug',
        'is_published',
        'published_at',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function translations(): HasMany
    {
        return $this->hasMany(BlogPostTranslation::class);
    }

    public function translation(?string $locale = null): ?BlogPostTranslation
    {
        $locale = $locale ?? app()->getLocale();

        return $this->translations()->where('locale', $locale)->first();
    }

    public function scopeWithTranslation($query, ?string $locale = null)
    {
        $locale = $locale ?? app()->getLocale();

        return $query->with([
            'translations' => function ($q) use ($locale) {
                $q->where('locale', $locale);
            },
        ]);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function title(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->translation()?->title
        );
    }

    public function excerpt(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->translation()?->excerpt
        );
    }

    public function content(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->translation()?->content
        );
    }
}
