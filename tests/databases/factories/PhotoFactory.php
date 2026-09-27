<?php

use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;
use Tests\Models\Photo;

/** @var Factory $factory */
$factory->define(Photo::class, function (Faker $faker) {
    return [
        'name' => $faker->unique()->name,
    ];
});
