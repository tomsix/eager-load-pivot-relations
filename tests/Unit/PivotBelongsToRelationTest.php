<?php

namespace TomSix\EagerLoadPivotRelations\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use TomSix\EagerLoadPivotRelations\Tests\Database\Factories\CarUserFactory;
use TomSix\EagerLoadPivotRelations\Tests\Models\Car;
use TomSix\EagerLoadPivotRelations\Tests\Models\CarUser;
use TomSix\EagerLoadPivotRelations\Tests\Models\Color;
use TomSix\EagerLoadPivotRelations\Tests\Models\Tire;
use TomSix\EagerLoadPivotRelations\Tests\Models\User;
use TomSix\EagerLoadPivotRelations\Tests\TestCase;

class PivotBelongsToRelationTest extends TestCase
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

    public function test_it_can_use_with_loaded_pivot_relations()
    {
        $user = User::factory()
            ->hasAttached(Car::factory()->count(2), [
                'color_id' => Color::factory()->create()->id,
            ])
            ->create();

        $user->cars()
            ->with([
                'pivot.color',
            ]);

        $this->assertInstanceOf(Car::class, $user->cars[0]);
        $this->assertInstanceOf(Color::class, $user->cars[0]->pivot->color);
    }

    public function test_it_can_use_load_pivot_relations()
    {
        $user = User::factory()->create();
        $pivots = CarUser::factory(['user_id' => $user->id])->count(2)->create();
        $tires = rand(4, 8);

        foreach ($pivots as $pivot) {
            Tire::factory(['car_user_id' => $pivot->id])
                ->count(1)
                ->create();
        }

        DB::enableQueryLog();

        $user->load([
            'cars',
            'cars.pivot.color',
        ]);

        $this->assertInstanceOf(Car::class, $user->cars[0]);
        $this->assertInstanceOf(CarUser::class, $user->cars[0]->pivot);
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

    public function test_it_can_use_with_custom_pivot_accessor_relations()
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

    public function test_it_can_use_load_custom__pivot_accessor_relations()
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

    public function test_it_can_use_load_missing_custom__pivot_accessor_relations()
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
