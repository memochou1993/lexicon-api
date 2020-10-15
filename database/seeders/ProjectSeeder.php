<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Traits\HasStaticAttributes;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
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
        $teams = app(TeamSeeder::class)->teams;

        $projects = $teams->reduce(function ($carry, $team) {
            $projects = Project::factory()->count(self::AMOUNT)->make();

            return $carry->merge($team->projects()->saveMany($projects));
        }, app(Collection::class));

        $this->set('projects', $projects);
    }
}
