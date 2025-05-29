<?php

namespace TomSix\EagerLoadPivotRelations\Tests\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use TomSix\EagerLoadPivotRelations\Tests\Models\Brand;
use TomSix\EagerLoadPivotRelations\Tests\Models\Car;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\TomSix\EagerLoadPivotRelations\Tests\Models\Car>
 */
class CarFactory extends Factory
{
    protected $model = Car::class;

    public function definition()
    {
        return [
            'model' => $this->faker->words(rand(2, 4), true),
            'brand_id' => function () {
                return Brand::factory()->create()->id;
            },
        ];
    }
}
