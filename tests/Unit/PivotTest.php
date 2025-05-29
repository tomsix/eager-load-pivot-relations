<?php

namespace TomSix\EagerLoadPivotRelations\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use TomSix\EagerLoadPivotRelations\Tests\Models\Car;
use TomSix\EagerLoadPivotRelations\Tests\Models\CarUser;
use TomSix\EagerLoadPivotRelations\Tests\Models\Color;
use TomSix\EagerLoadPivotRelations\Tests\Models\User;
use TomSix\EagerLoadPivotRelations\Tests\TestCase;

class PivotTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_use_with_pivot_relations()
    {
        $user = User::factory()->create();
        $pivots = CarUser::factory(['user_id' => $user->id])->count(2)->create();

        $user = User::with([
            'cars',
            'cars.pivot.color',
        ])
            ->find($user->id);

        $this->assertInstanceOf(Car::class, $user->cars[0]);
        $this->assertInstanceOf(Color::class, $user->cars[0]->pivot->color);
    }

    public function test_it_can_use_load_pivot_relations()
    {
        $user = User::factory()->create();
        $pivots = CarUser::factory(['user_id' => $user->id])->count(2)->create();

        $user->load([
            'cars',
            'cars.pivot.color',
        ]);

        $this->assertInstanceOf(Car::class, $user->cars[0]);
        $this->assertInstanceOf(Color::class, $user->cars[0]->pivot->color);
    }

    public function test_it_can_use_load_missing_pivot_relations()
    {
        $user = User::factory()->create();
        $pivots = CarUser::factory(['user_id' => $user->id])->count(2)->create();

        $user->loadMissing([
            'cars',
            'cars.pivot.color',
        ]);

        $this->assertInstanceOf(Car::class, $user->cars[0]);
        $this->assertInstanceOf(Color::class, $user->cars[0]->pivot->color);
    }

    public function test_it_can_use_with_custom_pivot_relations()
    {
        $car = Car::factory()->create();
        $pivots = CarUser::factory(['car_id' => $car->id])->count(2)->create();

        $car = Car::with([
            'users',
            'users.car_user.color',
        ])
            ->find($car->id);

        $this->assertInstanceOf(User::class, $car->users[0]);
        $this->assertInstanceOf(Color::class, $car->users[0]->car_user->color);
    }

    public function test_it_can_use_load_custom_pivot_relations()
    {
        $car = Car::factory()->create();
        $pivots = CarUser::factory(['car_id' => $car->id])->count(2)->create();

        $car->load([
            'users',
            'users.car_user.color',
        ]);

        $this->assertInstanceOf(User::class, $car->users[0]);
        $this->assertInstanceOf(Color::class, $car->users[0]->car_user->color);
    }

    public function test_it_can_use_load_missing_custom_pivot_relations()
    {
        $car = Car::factory()->create();
        $pivots = CarUser::factory(['car_id' => $car->id])->count(2)->create();

        $car->loadMissing([
            'users',
            'users.car_user.color',
        ]);

        $this->assertInstanceOf(User::class, $car->users[0]);
        $this->assertInstanceOf(Color::class, $car->users[0]->car_user->color);
    }
}
