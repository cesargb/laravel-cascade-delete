<?php

use Faker\Generator as Faker;
use Tests\Models\Option;

$factory->define(Option::class, function (Faker $faker) {
    return [
        'name' => $faker->unique()->colorName,
    ];
});
