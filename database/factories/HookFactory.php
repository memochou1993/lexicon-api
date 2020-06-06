<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Hook;
use Faker\Generator as Faker;

$factory->define(Hook::class, function (Faker $faker) {
    static $index = 0;

    $index++;

    return [
        'url' => config('app.url').DIRECTORY_SEPARATOR.$index,
    ];
});
