<?php

namespace audunru\EagerLoadPivotRelations\Tests\Database\Factories;

use audunru\EagerLoadPivotRelations\Tests\Models\Brand;
use Illuminate\Database\Eloquent\Factories\Factory;

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
