<?php

use App\Traits\HasStaticAttributes;
use Illuminate\Database\Seeder;

class ModelHasLanguageSeeder extends Seeder
{
    use HasStaticAttributes;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $languages = app(LanguageSeeder::class)->languages;

        app(TeamSeeder::class)->teams->each(function ($team) use ($languages) {
            $team->languages()->saveMany($languages);
        });

        app(ProjectSeeder::class)->projects->each(function ($project) use ($languages) {
            $project->languages()->saveMany($languages);
        });

        app(ValueSeeder::class)->values->each(function ($value, $index) use ($languages) {
            $value->languages()->save($languages->get($index % $languages->count()));
        });
    }
}
