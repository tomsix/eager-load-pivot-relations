<?php

namespace TomSix\EagerLoadPivotRelations\Tests\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use TomSix\EagerLoadPivotRelations\EagerLoadPivotTrait;
use TomSix\EagerLoadPivotRelations\Tests\Database\Factories\UserFactory;

class User extends Authenticatable
{
    use EagerLoadPivotTrait;
    use HasFactory;

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function cars(): BelongsToMany
    {
        return $this->belongsToMany(Car::class, CarUser::class)
            ->withPivot('color_id');
    }

    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }
}
