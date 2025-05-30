<?php

namespace TomSix\EagerLoadPivotRelations\Tests\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use TomSix\EagerLoadPivotRelations\Tests\Database\Factories\TireFactory;

class Tire extends Model
{
    use HasFactory;

    protected $table = 'tires';

    protected $fillable = [
        'brand',
        'profile_depth',
        'car_user_id',
    ];

    protected static function newFactory(): TireFactory
    {
        return TireFactory::new();
    }
}
