<?php

namespace TomSix\EagerLoadPivotRelations\Tests\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use TomSix\EagerLoadPivotRelations\Tests\Database\Factories\ColorFactory;

class Color extends Model
{
    use HasFactory;

    protected $table = 'colors';

    protected $fillable = [
        'name',
    ];

    public function cars(): BelongsToMany
    {
        return $this->belongsToMany(Car::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    protected static function newFactory(): ColorFactory
    {
        return ColorFactory::new();
    }
}
