<?php

use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;
use Tests\Models\Video;

/** @var Factory $factory */
$factory->define(Video::class, function (Faker $faker) {
    return [
        'name' => $faker->unique()->firstName,
    ];
});
