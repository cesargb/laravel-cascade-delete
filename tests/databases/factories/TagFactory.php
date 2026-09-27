<?php

use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;
use Tests\Models\Tag;

/** @var Factory $factory */
$factory->define(Tag::class, function (Faker $faker) {
    return [
        'name' => $faker->unique()->countryCode,
    ];
});
