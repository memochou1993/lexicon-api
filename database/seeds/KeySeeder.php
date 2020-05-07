<?php

use App\Models\Key;
use App\Traits\HasStaticAttributes;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;

class KeySeeder extends Seeder
{
    use HasStaticAttributes;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $projects = app(ProjectSeeder::class)->projects;

        $this->keys = $projects->reduce(function ($carry, $project) {
            return $carry->merge(
                $project->keys()->saveMany(
                    factory(Key::class, 10)->withoutEvents()->make()
                )
            );
        }, app(Collection::class));
    }
}
