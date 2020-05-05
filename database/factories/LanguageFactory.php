<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Language;
use Faker\Generator as Faker;

$factory->define(Language::class, function (Faker $faker) {
    static $index = 0;

    $index++;

    return [
        'name' => 'Language '.$index,
    ];
});
