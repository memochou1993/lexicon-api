<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Key;
use Faker\Generator as Faker;

$factory->define(Key::class, function (Faker $faker) {
    static $index = 0;

    $index++;

    return [
        'name' => 'Key '.$index,
    ];
});
