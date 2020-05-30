<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Form;
use Faker\Generator as Faker;

$factory->define(Form::class, function (Faker $faker) {
    static $index = 0;

    $index++;

    return [
        'name' => 'Form '.$index,
        'range_min' => pow(10, $index - 1) + ((int) $index > 1),
        'range_max' => pow(10, $index),
    ];
});
