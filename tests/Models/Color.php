<?php

namespace TomSix\EagerLoadPivotRelations\Tests\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use TomSix\EagerLoadPivotRelations\Tests\Database\Factories\ColorFactory;

class Color extends Model
{
    use HasFactory;

    protected $table = 'colors';

    protected $fillable = [
        'name',
    ];

    public function cars()
    {
        return $this->belongsToMany(Car::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    protected static function newFactory()
    {
        return ColorFactory::new();
    }
}
