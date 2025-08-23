<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Scout\Searchable;

class Composer extends Model
{
    /** @use HasFactory<\Database\Factories\ComposerFactory> */
    use HasFactory;

    use Searchable;

    protected $fillable = ['name'];

    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }

    public function tunes(): HasMany
    {
        return $this->hasMany(Tune::class);
    }

    public function origin()
    {
        return $this->belongsTo(Origin::class);
    }

    public function name(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => mb_convert_case($value, MB_CASE_TITLE, 'UTF-8'),
            set: fn ($value) => mb_strtolower($value)
        );
    }
}
