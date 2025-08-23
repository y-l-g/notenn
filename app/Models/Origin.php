<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Origin extends Model
{
    protected $fillable = ['name', 'type', 'parent_id'];

    protected $appends = ['name'];

    public function composers()
    {
        return $this->hasMany(Composer::class);
    }

    public function tunes()
    {
        return $this->hasMany(Tune::class);
    }

    public function parent()
    {
        return $this->belongsTo(Origin::class);
    }

    public function children()
    {
        return $this->hasMany(Origin::class, 'parent_id');
    }

    public function country()
    {
        if ($this->type === 'country') {
            return $this;
        }

        return $this->parent?->country();
    }

    public function continent()
    {
        if ($this->type === 'continent') {
            return $this;
        }

        return $this->parent?->continent();
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
