<?php

namespace TomSix\EagerLoadPivotRelations\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use TomSix\EagerLoadPivotRelations\Tests\Models\Car;
use TomSix\EagerLoadPivotRelations\Tests\Models\CarUser;
use TomSix\EagerLoadPivotRelations\Tests\Models\Color;
use TomSix\EagerLoadPivotRelations\Tests\Models\User;
use TomSix\EagerLoadPivotRelations\Tests\TestCase;

class PaginateTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_paginate_with_pivot_relations()
    {
        $pivots = CarUser::factory()->count(30)->create();

        $users = User::with([
            'cars',
            'cars.pivot.color',
        ])
            ->paginate(10);

        $this->assertInstanceOf(LengthAwarePaginator::class, $users);

        // When iterating the cars, the pivot relation should not load tires
        $this->expectsDatabaseQueryCount(0);
        foreach ($users as $user) {
            foreach ($user->cars as $car) {
                $this->assertInstanceOf(Color::class, $car->pivot->color);
            }
        }
    }

    public function test_it_can_paginate_after_eager_loading_pivot_relations()
    {
        $user = User::factory()
            ->hasAttached(Car::factory()->count(30), [
                'color_id' => Color::factory()->create()->id,
            ])
            ->create();

        $cars = $user->cars()->with(['pivot.color'])->paginate(10);

        $this->assertInstanceOf(LengthAwarePaginator::class, $cars);

        // When iterating the cars, the pivot relation should not load the color
        $this->expectsDatabaseQueryCount(0);
        foreach ($cars as $car) {
            $this->assertInstanceOf(Color::class, $car->pivot->color);
        }
    }

    public function test_it_can_paginate_with_custom__pivot_accessor_relations()
    {
        $pivots = CarUser::factory()->count(30)->create();

        $cars = Car::with([
            'users',
            'users.car_user.color',
        ])
            ->paginate(10);

        $this->assertInstanceOf(LengthAwarePaginator::class, $cars);
    }

    public function test_it_can_paginate_after_eager_loading_custom__pivot_accessor_relations()
    {
        $pivots = CarUser::factory()->count(30)->create();

        $car = Car::find(1)->users()->with(['car_user.color'])->paginate(10);

        $this->assertInstanceOf(LengthAwarePaginator::class, $car);
    }
}
