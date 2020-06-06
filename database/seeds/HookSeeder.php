<?php

use App\Models\Hook;
use App\Traits\HasStaticAttributes;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;

class HookSeeder extends Seeder
{
    use HasStaticAttributes;

    public const DATA_AMOUNT = 2;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $projects = app(ProjectSeeder::class)->projects;

        $hooks = $projects->reduce(function ($carry, $project) {
            $hooks = factory(Hook::class, self::DATA_AMOUNT)->make();

            return $carry->merge($project->hooks()->saveMany($hooks));
        }, app(Collection::class));

        $this->set('hooks', $hooks);
    }
}
