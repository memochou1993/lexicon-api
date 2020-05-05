<?php

use App\Traits\HasStaticAttributes;
use Illuminate\Database\Seeder;

class ModelHasFormSeeder extends Seeder
{
    use HasStaticAttributes;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $forms = app(FormSeeder::class)->forms;

        app(TeamSeeder::class)->teams->each(function ($team) use ($forms) {
            $team->forms()->saveMany($forms);
        });

        app(LanguageSeeder::class)->languages->each(function ($language) use ($forms) {
            $language->forms()->saveMany($forms);
        });
    }
}
