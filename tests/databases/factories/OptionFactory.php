<?php

use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;
use Tests\Models\Option;

/** @var Factory $factory */
$factory->define(Option::class, function (Faker $faker) {
    return [
        'name' => $faker->unique()->colorName,
    ];
});
