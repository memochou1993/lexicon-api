<?php

namespace Database\Seeders;

use App\Traits\HasStaticAttributes;
use Illuminate\Database\Seeder;

class ModelHasUserSeeder extends Seeder
{
    use HasStaticAttributes;

    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        $users = app(UserSeeder::class)->users;

        app(TeamSeeder::class)->teams->each(function ($team) use ($users) {
            $team->users()->saveMany($users);
        });

        app(ProjectSeeder::class)->projects->each(function ($project) use ($users) {
            $project->users()->saveMany($users);
        });

        $users->first()->teams()->update(['is_owner' => true]);
        $users->first()->projects()->update(['is_owner' => true]);
    }
}
