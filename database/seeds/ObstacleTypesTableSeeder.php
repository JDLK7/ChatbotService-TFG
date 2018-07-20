<?php

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ObstacleTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('obstacle_types')->insert([
            [
                'id' => Str::uuid(),
                'value' => 'stair',
                'name' => 'Escalera sin vía alternativa'
            ],
            [
                'id' => Str::uuid(),
                'value' => 'crash',
                'name' => 'Accidente'
            ],
            [
                'id' => Str::uuid(),
                'value' => 'slope',
                'name' => 'Rampa con pendiente excesiva'
            ],
            [
                'id' => Str::uuid(),
                'value' => 'parking',
                'name' => 'Aparcamiento sin plaza para minusválidos'
            ],
            [
                'id' => Str::uuid(),
                'value' => 'other',
                'name' => 'Otro'
            ],
        ]);
    }
}
