<?php

namespace TomSix\EagerLoadPivotRelations\Tests\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use TomSix\EagerLoadPivotRelations\Tests\Models\Brand;

class BrandFactory extends Factory
{
    protected $model = Brand::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'logo' => $this->faker->imageUrl,
        ];
    }
}
