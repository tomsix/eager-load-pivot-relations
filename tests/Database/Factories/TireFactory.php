<?php

namespace TomSix\EagerLoadPivotRelations\Tests\Database\Factories;

use TomSix\EagerLoadPivotRelations\Tests\Models\CarUser;
use TomSix\EagerLoadPivotRelations\Tests\Models\Tire;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\TomSix\EagerLoadPivotRelations\Tests\Models\Tire>
 */
class TireFactory extends Factory
{
    protected $model = Tire::class;

    public function definition()
    {
        return [
            'brand'         => $this->faker->word,
            'profile_depth' => $this->faker->randomNumber(2),
            'car_user_id'   => function () {
                return CarUser::factory()->create()->id;
            },
        ];
    }
}
