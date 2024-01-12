<?php

namespace audunru\EagerLoadPivotRelations\Tests\Database\Factories;

use audunru\EagerLoadPivotRelations\Tests\Models\Color;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\audunru\EagerLoadPivotRelations\Tests\Models\Color>
 */
class ColorFactory extends Factory
{
    protected $model = Color::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word,
        ];
    }
}
