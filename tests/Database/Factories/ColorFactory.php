<?php

namespace TomSix\EagerLoadPivotRelations\Tests\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use TomSix\EagerLoadPivotRelations\Tests\Models\Color;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\TomSix\EagerLoadPivotRelations\Tests\Models\Color>
 */
class ColorFactory extends Factory
{
    protected $model = Color::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
        ];
    }
}
