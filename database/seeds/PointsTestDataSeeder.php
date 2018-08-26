<?php

use App\User;
use App\Point;
use App\CrosswalkPoint;
use Illuminate\Support\Carbon;
use Illuminate\Database\Seeder;

class PointsTestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $visibilityGrades = [
            'good', 'normal', 'bad',
        ];
        $currentMonth = Carbon::now()->month;
        $fakeUser = factory(User::class)->create();

        // Crea 300 puntos con un mes aleatorio para las fechas.
        for ($i = 0; $i < 300; $i++) {
            $date = Carbon::now()->month(rand(1, 12));

            $point = factory(CrosswalkPoint::class)->create([
                'latitude' => mt_rand(38381494, 38390400) / 1000000,
                'longitude' => mt_rand(508847, 518095) * (-1) / 1000000,
                'created_at' => $date,
                'updated_at' => $date,
            ]);
        }

        // Crea versiones de un número aleatorio de puntos para cada mes.
        for ($i = 1; $i < $currentMonth; $i++) {
            $pointsCount = Point::whereMonth('created_at', $i)->count();
            $points = Point::whereMonth('created_at', $i)
                ->take(mt_rand(0, $pointsCount))->get();

            foreach ($points as $point) {
                $revision = $point->makeVersion($fakeUser);
                $revision->created_at = $point->created_at;
                $revision->updated_at = $point->updated_at;
                $revision->properties = (object) [
                    'hasCurbRamps' => mt_rand(0, 1) == 1,
                    'visibility' => $visibilityGrades[mt_rand(0, 2)],
                    'hasSemaphore' => mt_rand(0, 1) == 1,
                ];
                $revision->save();
            }
        }
    }
}
