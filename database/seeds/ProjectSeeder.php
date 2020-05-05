<?php

use App\Models\Project;
use App\Traits\HasStaticAttributes;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    use HasStaticAttributes;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $teams = app(TeamSeeder::class)->teams;

        $this->projects = $teams->reduce(function ($carry, $team) {
            return $carry->merge(
                $team->projects()->saveMany(
                    factory(Project::class, 5)->make()
                )
            );
        }, app(Collection::class));
    }
}
