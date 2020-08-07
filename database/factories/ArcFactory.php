<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Arc;
use Faker\Generator as Faker;

$factory->define(Arc::class, function (Faker $faker) {
    return [
        'name' => $faker->title,
        'color' => $faker->hexColor,
    ];
});
