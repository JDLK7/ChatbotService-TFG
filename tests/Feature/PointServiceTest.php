<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\PointService;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PointServiceTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var \App\Services\PointService
     */
    protected $pointService;

    /** @before */
    public function setUpFixtures()
    {
        $this->pointService = new PointService();
    }

    public function test_crosswalk_details_only_works_for_crosswalk_points() {
        $this->expectException(\TypeError::class);

        $point = factory(\App\WorksPoint::class)->create();
        $point = $this->pointService->crosswalkPointWithDetails($point);
    }

    public function test_crosswalk_details_for_point_without_versions() {
        $point = factory(\App\CrosswalkPoint::class)->create();
        $point = $this->pointService->crosswalkPointWithDetails($point);

        $expectedProperties = [
            'visibility' => ['bad' => 0, 'normal' => 0, 'good' => 0],
            'hasCurbRamps' => ['true' => 0, 'false' => 0],
            'hasSemaphore' => ['true' => 0, 'false' => 0],
        ];

        $this->assertEquals($expectedProperties, $point->properties);
    }

    public function test_crosswalk_details_for_point_with_multiple_versions() {
        $point = factory(\App\CrosswalkPoint::class)->create();
        
        for ($i = 0; $i < 4; $i++) {
            $user = factory(\App\User::class)->create();
            $version = $point->makeVersion($user);
            $version->properties = (object) [
                'hasCurbRamps' => false,
                'visibility' => ($i >= 2) ? 'good' : 'normal',
                'hasSemaphore' => false,
            ];
            $version->save();
        }

        $point = $this->pointService->crosswalkPointWithDetails($point);

        $expectedProperties = [
            'visibility' => ['bad' => 0, 'normal' => 2, 'good' => 2],
            'hasCurbRamps' => ['true' => 0, 'false' => 4],
            'hasSemaphore' => ['true' => 0, 'false' => 4],
        ];

        $this->assertEquals($expectedProperties, $point->properties);
    }
}
