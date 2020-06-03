<?php

use App\Models\Project;
use App\Traits\HasStaticAttributes;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    use HasStaticAttributes;

    public const DATA_AMOUNT = 5;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $teams = app(TeamSeeder::class)->teams;

        $projects = $teams->reduce(function ($carry, $team) {
            $projects = factory(Project::class, self::DATA_AMOUNT)->disableEvents()->make();

            return $carry->merge($team->projects()->saveMany($projects));
        }, app(Collection::class));

        $this->set('projects', $projects);
    }
}
