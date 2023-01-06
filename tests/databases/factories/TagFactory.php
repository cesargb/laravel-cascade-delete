<?php

use Faker\Generator as Faker;
use Tests\Models\Tag;

/** @var \Illuminate\Database\Eloquent\Factory $factory */

$factory->define(Tag::class, function (Faker $faker) {
    return [
        'name' => $faker->unique()->countryCode,
    ];
});
