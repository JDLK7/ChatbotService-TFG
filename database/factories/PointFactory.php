<?php

use Faker\Generator as Faker;

$factory->define(App\Point::class, function (Faker $faker) {
    return [
        'latitude' => $faker->randomFloat(7, -180, 180),
        'longitude' => $faker->randomFloat(7, -180, 180),
    ];
});

$factory->define(App\WorksPoint::class, function (Faker $faker) {
    return [
        'latitude' => $faker->randomFloat(7, -180, 180),
        'longitude' => $faker->randomFloat(7, -180, 180),
    ];
});

$factory->define(App\CrosswalkPoint::class, function (Faker $faker) {
    return [
        'latitude' => $faker->randomFloat(7, -180, 180),
        'longitude' => $faker->randomFloat(7, -180, 180),
    ];
});

$factory->define(App\UrbanFurniturePoint::class, function (Faker $faker) {
    return [
        'latitude' => $faker->randomFloat(7, -180, 180),
        'longitude' => $faker->randomFloat(7, -180, 180),
    ];
});
