<?php

namespace Database\Seeders;

use App\Traits\HasStaticAttributes;
use Illuminate\Database\Seeder;

class ModelHasFormSeeder extends Seeder
{
    use HasStaticAttributes;

    /**
     * Run the database seeders.
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

        app(ValueSeeder::class)->values->each(function ($value, $index) use ($forms) {
            $value->forms()->save($forms->get($index % $forms->count()));
        });
    }
}
