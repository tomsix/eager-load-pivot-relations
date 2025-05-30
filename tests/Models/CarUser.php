<?php

namespace TomSix\EagerLoadPivotRelations\Tests\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\Pivot;
use TomSix\EagerLoadPivotRelations\Tests\Database\Factories\CarUserFactory;

class CarUser extends Pivot
{
    use HasFactory;

    public $incrementing = true;

    protected $table = 'car_user';

    protected $fillable = [
        'car_id',
        'color_id',
        'user_id',
    ];

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }

    public function color(): BelongsTo
    {
        return $this->belongsTo(Color::class);
    }

    public function tires(): HasMany
    {
        return $this->hasMany(Tire::class, 'car_user_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function newFactory(): CarUserFactory
    {
        return CarUserFactory::new();
    }
}
