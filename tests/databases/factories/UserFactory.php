<?php

use Faker\Generator as Faker;
use Tests\Models\User;

$factory->define(User::class, function (Faker $faker) {
    return [
        'name' => $faker->unique()->name,
    ];
});