<?php

use Faker\Generator as Faker;
use Tests\Models\Photo;

$factory->define(Photo::class, function (Faker $faker) {
    return [
        'name' => $faker->unique()->name,
    ];
});