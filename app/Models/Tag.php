<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function arrangements(): MorphToMany
    {
        return $this->morphToMany(Arrangement::class, 'taggable');
    }

    public function tunebooks(): MorphToMany
    {
        return $this->morphToMany(Tunebook::class, 'taggable');
    }

    public function name(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => mb_convert_case($value, MB_CASE_TITLE, 'UTF-8'),
            set: fn ($value) => mb_strtolower($value)
        );
    }
}
