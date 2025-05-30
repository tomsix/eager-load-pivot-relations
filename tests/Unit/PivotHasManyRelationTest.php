<?php

namespace TomSix\EagerLoadPivotRelations\Tests\Unit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use TomSix\EagerLoadPivotRelations\Tests\Models\CarUser;
use TomSix\EagerLoadPivotRelations\Tests\Models\Tire;
use TomSix\EagerLoadPivotRelations\Tests\Models\User;
use TomSix\EagerLoadPivotRelations\Tests\TestCase;

class PivotHasManyRelationTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_use_with_pivot_relations()
    {
        $user = User::factory()->create();
        $tires = rand(4, 8);
        $pivots = CarUser::factory()
            ->for($user)
            ->has(Tire::factory()->count($tires))
            ->count(30)
            ->create();

        Model::preventLazyLoading();

        DB::enableQueryLog();

        $cars = $user
            ->cars()
            ->withPivot('id')
            ->with([
                'pivot.tires',
            ])
            ->paginate();

        // When iterating the cars, the pivot relation should not load tires
        $this->expectsDatabaseQueryCount(0);

        foreach ($cars as $car) {
            $this->assertCount($tires, $car->pivot->tires);
        }
    }
}
