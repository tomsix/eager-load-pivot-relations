<?php

namespace TomSix\EagerLoadPivotRelations\Tests\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use TomSix\EagerLoadPivotRelations\EagerLoadPivotBelongsToMany;
use TomSix\EagerLoadPivotRelations\EagerLoadPivotTrait;
use TomSix\EagerLoadPivotRelations\Tests\Database\Factories\CarFactory;

class Car extends Model
{
    use EagerLoadPivotTrait;
    use HasFactory;

    protected $table = 'cars';

    protected $fillable = [
        'model',
        'brand_id',
    ];

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function users(): EagerLoadPivotBelongsToMany
    {
        return $this->belongsToMany(User::class, CarUser::class)
            ->withPivot('color_id')
            ->as('car_user');
    }

    protected static function newFactory(): CarFactory
    {
        return CarFactory::new();
    }
}
