<?php

use Faker\Generator as Faker;
use Tests\Models\Option;

/** @var \Illuminate\Database\Eloquent\Factory $factory */

$factory->define(Option::class, function (Faker $faker) {
    return [
        'name' => $faker->unique()->colorName,
    ];
});
