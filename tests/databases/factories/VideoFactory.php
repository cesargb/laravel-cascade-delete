<?php

use Faker\Generator as Faker;
use Tests\Models\Video;

/** @var \Illuminate\Database\Eloquent\Factory $factory */

$factory->define(Video::class, function (Faker $faker) {
    return [
        'name' => $faker->unique()->firstName,
    ];
});
