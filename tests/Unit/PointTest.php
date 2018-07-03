<?php

namespace Tests\Unit;

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
}
