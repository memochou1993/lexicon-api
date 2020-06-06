<?php

use App\Traits\HasStaticAttributes;
use Illuminate\Database\Seeder;

class ModelHasHookSeeder extends Seeder
{
    use HasStaticAttributes;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        app(ProjectSeeder::class)->projects->each(function ($project) {
            $project->hooks()->create([
                'url' => config('app.url'),
            ]);
        });
    }
}
