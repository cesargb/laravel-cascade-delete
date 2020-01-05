<?php

use Faker\Generator as Faker;
use Tests\Models\Tag;

$factory->define(Tag::class, function (Faker $faker) {
    return [
        'name' => $faker->unique()->countryCode,
    ];
});
