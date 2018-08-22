<?php

namespace Tests\Feature;

use App\User;
use App\Point;
use Tests\TestCase;
use App\CrosswalkPoint;
use App\Exceptions\NoPointsException;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class NearestPointTest extends TestCase
{
    use DatabaseTransactions;

    public function test_it_gets_nearest_point() {
        $user = factory(User::class)->create();
        $point = factory(CrosswalkPoint::class)->create();

        // La ubicación del usuario será la misma que la del punto
        // para asegurar que se selecciona como el más cercano.
        $userLocation = new Point([
            'latitude' => $point->latitude,
            'longitude' => $point->longitude,
        ]);
        $nearestPoint = $userLocation->nearestPoint();

        $this->assertEquals($point->id, $nearestPoint->id);
    }

    public function test_it_gets_nearest_point_avoinding_reviewed_by_user() {
        $user = factory(\App\User::class)->create();
        $point = factory(\App\CrosswalkPoint::class)->create();
        $fallbackPoint = factory(\App\CrosswalkPoint::class)->create();

        $this->actingAs($user);

        // El usuario revisa el punto
        $point->createVersion($user);

        // La ubicación del usuario será la misma que la del punto
        // para asegurar que se selecciona como el más cercano.
        $userLocation = new \App\Point([
            'latitude' => $point->latitude,
            'longitude' => $point->longitude,
        ]);
        $nearestPoint = $userLocation->nearestPoint();

        // Como el usuario lo ha revisado, no debería
        // aparecer como el punto más cercano.
        $this->assertNotEquals($point->id, $nearestPoint->id);
    }

    public function test_it_fails_if_there_are_no_points_stored() {
        $this->expectException(NoPointsException::class);

        $user = factory(User::class)->create();
        $point = factory(CrosswalkPoint::class)->make();

        $nearestPoint = $point->nearestPoint();
    }
}
