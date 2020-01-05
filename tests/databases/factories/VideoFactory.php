<?php

use Faker\Generator as Faker;
use Tests\Models\Video;

$factory->define(Video::class, function (Faker $faker) {
    return [
        'name' => $faker->unique()->firstName,
    ];
});