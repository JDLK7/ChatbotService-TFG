<?php

namespace Tests\Unit;

use App\User;
use App\Point;
use Tests\TestCase;
use App\WorksPoint;
use App\CrosswalkPoint;
use App\UrbanFurniturePoint;
use App\Exceptions\PointFactoryException;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PointTest extends TestCase
{
    use RefreshDatabase;

    public function test_point_factory() {
        $point = Point::make('crosswalk');
        $this->assertTrue(is_a($point, CrosswalkPoint::class));
        $point = Point::make('works');
        $this->assertTrue(is_a($point, WorksPoint::class));
        $point = Point::make('urbanFurniture');
        $this->assertTrue(is_a($point, UrbanFurniturePoint::class));
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
}
