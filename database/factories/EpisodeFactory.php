<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Arc;
use App\Episode;
use Faker\Generator as Faker;

$factory->define(Episode::class, function (Faker $faker) {
    return [
        'title' => $faker->title,
        'videoId' => 'cG7FkoNKBzI', // scl - oec
        'arc_id' => function () {
            return factory(Arc::class)->create()->id;
        },
    ];
});
