<?php

namespace Database\Seeders;

use App\Models\Hook;
use App\Traits\HasStaticAttributes;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;

class HookSeeder extends Seeder
{
    use HasStaticAttributes;

    public const AMOUNT = 1;

    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        $projects = app(ProjectSeeder::class)->projects;

        $hooks = $projects->reduce(function ($carry, $project) {
            $hooks = Hook::factory()->count(self::AMOUNT)->make();

            return $carry->merge($project->hooks()->saveMany($hooks));
        }, app(Collection::class));

        $this->set('hooks', $hooks);
    }
}
