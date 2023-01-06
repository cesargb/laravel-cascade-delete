<?php

use Faker\Generator as Faker;
use Tests\Models\Photo;

/** @var \Illuminate\Database\Eloquent\Factory $factory */

$factory->define(Photo::class, function (Faker $faker) {
    return [
        'name' => $faker->unique()->name,
    ];
});
