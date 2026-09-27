<?php

use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;
use Tests\Models\User;

/** @var Factory $factory */
$factory->define(User::class, function (Faker $faker) {
    return [
        'name' => $faker->unique()->name,
    ];
});
