<?php

use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;
use Tests\Models\Image;

/** @var Factory $factory */
$factory->define(Image::class, function (Faker $faker) {
    return [
        'name' => $faker->unique()->userName.'.jpg',
    ];
});
