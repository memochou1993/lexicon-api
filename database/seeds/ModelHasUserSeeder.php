<?php

use App\Traits\HasStaticAttributes;
use Illuminate\Database\Seeder;

class ModelHasUserSeeder extends Seeder
{
    use HasStaticAttributes;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = app(UserSeeder::class)->users;

        app(RoleSeeder::class)->roles->each(function ($role) use ($users) {
            $role->users()->saveMany($users);
        });

        app(TeamSeeder::class)->teams->each(function ($team) use ($users) {
            $team->users()->saveMany($users);
        });

        app(ProjectSeeder::class)->projects->each(function ($project) use ($users) {
            $project->users()->saveMany($users);
        });
    }
}
