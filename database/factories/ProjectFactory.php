<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Project;
use Faker\Generator as Faker;

$factory->define(Project::class, function (Faker $faker) {
    static $index = 0;

    $index++;

    return [
        'name' => 'Project '.$index,
        'api_keys' => json_encode([
            'public_key' => $faker->md5,
            'secret_key' => $faker->md5,
        ]),
    ];
});
