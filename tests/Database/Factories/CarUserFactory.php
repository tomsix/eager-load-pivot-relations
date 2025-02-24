<?php

namespace TomSix\EagerLoadPivotRelations\Tests\Database\Factories;

use TomSix\EagerLoadPivotRelations\Tests\Models\Car;
use TomSix\EagerLoadPivotRelations\Tests\Models\CarUser;
use TomSix\EagerLoadPivotRelations\Tests\Models\Color;
use TomSix\EagerLoadPivotRelations\Tests\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\TomSix\EagerLoadPivotRelations\Tests\Models\CarUser>
 */
class CarUserFactory extends Factory
{
    protected $model = CarUser::class;

    public function definition()
    {
        return [
            'car_id'   => function () {
                return Car::factory()->create()->id;
            },
            'color_id' => function () {
                return Color::factory()->create()->id;
            },
            'user_id'  => function () {
                return User::factory()->create()->id;
            },
        ];
    }
}
