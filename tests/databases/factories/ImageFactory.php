<?php

use Faker\Generator as Faker;
use Tests\Models\Image;

$factory->define(Image::class, function (Faker $faker) {
    return [
        'name' => $faker->unique()->userName.'.jpg',
    ];
});
