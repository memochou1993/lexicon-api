<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Permission;
use Faker\Generator as Faker;

$factory->define(Permission::class, function (Faker $faker) {
    static $index = 0;

    $index++;

    return [
        'name' => 'Permission '.$index,
    ];
});
