<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rhythm extends Model
{
    /** @use HasFactory<\Database\Factories\RhythmFactory> */
    use HasFactory;

    protected $fillable = ['name'];

    protected $appends = ['name'];

    public function arrangements(): HasMany
    {
        return $this->hasMany(Arrangement::class);
    }

    public function nameEn(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => mb_convert_case($value, MB_CASE_TITLE, 'UTF-8'),
            set: fn ($value) => mb_strtolower($value)
        );
    }

    public function nameFr(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => mb_convert_case($value, MB_CASE_TITLE, 'UTF-8'),
            set: fn ($value) => mb_strtolower($value)
        );
    }

    public function nameEs(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => mb_convert_case($value, MB_CASE_TITLE, 'UTF-8'),
            set: fn ($value) => mb_strtolower($value)
        );
    }

    public function nameBr(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => mb_convert_case($value, MB_CASE_TITLE, 'UTF-8'),
            set: fn ($value) => mb_strtolower($value)
        );
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            get: function () {
                return match (app()->getLocale()) {
                    'fr' => $this->name_fr ?? null,
                    'es' => $this->name_es ?? null,
                    'br' => $this->name_br ?? null,
                    default => $this->name_en ?? null,
                };
            }
        );
    }
}
