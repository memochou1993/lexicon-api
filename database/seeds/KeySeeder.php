<?php

use App\Models\Key;
use App\Traits\HasStaticAttributes;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;

class KeySeeder extends Seeder
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
        $projects = app(ProjectSeeder::class)->projects;

        $keys = $projects->reduce(function ($carry, $project) {
            $keys = factory(Key::class, self::DATA_AMOUNT)->disableEvents()->make();

            return $carry->merge($project->keys()->saveMany($keys));
        }, app(Collection::class));

        $this->set('keys', $keys);
    }
}
