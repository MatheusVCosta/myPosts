<?php

use App\Photo;
use Faker\Generator as Faker;

$factory->define(Photo::class, function (Faker $faker) {
    return [
        'path' => 'user/',
        'name' => $faker->name,
        'type' => "user"
    ];
});
