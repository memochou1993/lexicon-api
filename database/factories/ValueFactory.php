<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Value;
use Faker\Generator as Faker;

$factory->define(Value::class, function (Faker $faker) {
    static $index = 0;

    $index++;

    return [
        'text' => 'Value '.$index,
    ];
});
