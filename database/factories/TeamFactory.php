<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Team;
use Faker\Generator as Faker;

$factory->define(Team::class, function (Faker $faker) {
    static $index = 0;

    $index++;

    return [
        'name' => 'Team '.$index,
    ];
});
