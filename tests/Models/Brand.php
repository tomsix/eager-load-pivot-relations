<?php

namespace TomSix\EagerLoadPivotRelations\Tests\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use TomSix\EagerLoadPivotRelations\Tests\Database\Factories\BrandFactory;

class Brand extends Model
{
    use HasFactory;

    protected $table = 'brands';

    protected $fillable = [
        'name',
        'logo',
    ];

    public function cars()
    {
        return $this->hasMany(Car::class);
    }

    protected static function newFactory()
    {
        return BrandFactory::new();
    }
}
