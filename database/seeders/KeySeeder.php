<?php

namespace Database\Seeders;

use App\Models\Key;
use App\Traits\HasStaticAttributes;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;

class KeySeeder extends Seeder
{
    use HasStaticAttributes;

    public const AMOUNT = 5;

    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        $projects = app(ProjectSeeder::class)->projects;

        $keys = $projects->reduce(function ($carry, $project) {
            $keys = Key::factory()->count(self::AMOUNT)->make();

            return $carry->merge($project->keys()->saveMany($keys));
        }, app(Collection::class));

        $this->set('keys', $keys);
    }
}
