<?php

namespace Tests\Unit;

use App\User;
use App\Point;
use Tests\TestCase;
use App\WorksPoint;
use App\ObstaclePoint;
use App\CrosswalkPoint;
use App\UrbanFurniturePoint;
use App\Exceptions\PointFactoryException;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PointTest extends TestCase
{
    use DatabaseTransactions;

    public function test_display_name_accessor() {
        $point = factory(CrosswalkPoint::class)->make();
        $this->assertEquals(__('points/types.crosswalk'), $point->displayName);
        $point = factory(WorksPoint::class)->make();
        $this->assertEquals(__('points/types.works'), $point->displayName);
        $point = factory(UrbanFurniturePoint::class)->make();
        $this->assertEquals(__('points/types.urbanFurniture'), $point->displayName);
        $point = factory(ObstaclePoint::class)->make();
        $this->assertEquals(__('points/types.obstacle'), $point->displayName);
    }

    public function test_lat_lng_accessors() {
        $point = factory(Point::class)->make();

        $this->assertEquals($point->latitude, $point->lat);
        $this->assertEquals($point->longitude, $point->lng);
    }

    public function test_point_factory() {
        $point = Point::make('crosswalk');
        $this->assertTrue(is_a($point, CrosswalkPoint::class));
        $point = Point::make('works');
        $this->assertTrue(is_a($point, WorksPoint::class));
        $point = Point::make('urbanFurniture');
        $this->assertTrue(is_a($point, UrbanFurniturePoint::class));
        $point = Point::make('obstacle');
        $this->assertTrue(is_a($point, ObstaclePoint::class));
    }

    public function test_point_factory_fails_on_unknown_type() {
        $this->expectException(PointFactoryException::class);

        $point = Point::make(str_random(10));
    }

    public function test_new_point_subclass_comes_with_version() {
        $point = factory(CrosswalkPoint::class)->create();

        $this->assertCount(1, $point->versions);
    }

    public function test_it_creates_point_version_with_associated_user() {
        $point = factory(CrosswalkPoint::class)->create();
        $user = factory(User::class)->create();

        $version = $point->createVersion($user);

        $this->assertNotNull($version->user);
    }

    /**
     * Comprueba que la precisión del método sea de <= 0.5m cuando
     * la distancia entre los puntos es de aproximadamente 20m.
     */
    public function test_distance_between_two_points() {
        $point1 = factory(Point::class)->make([
            'latitude' => 38.594804,
            'longitude' => -0.674691,
        ]);
        $point2 = factory(Point::class)->make([
            'latitude' => 38.594949,
            'longitude' => -0.674495,
        ]);

        // Distancia calculada con servicio online
        $expectedDistance = 23;
        $actualDistance = $point1->distanceTo($point2);

        $this->assertEquals($expectedDistance, $actualDistance, '', 0.5);
    }
}
