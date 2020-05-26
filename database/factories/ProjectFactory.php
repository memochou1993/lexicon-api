<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Project;
use Faker\Generator as Faker;

$factory->define(Project::class, function (Faker $faker) {
    static $index = 0;

    $index++;

    return [
        'name' => 'Project '.$index,
        // TODO
        // 'api_keys' => json_encode([
        //     'project_id' => $faker->md5,
        //     'read_key' => $faker->md5,
        //     'write_key' => $faker->md5,
        // ]),
    ];
});
