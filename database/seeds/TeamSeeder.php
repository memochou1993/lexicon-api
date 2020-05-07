<?php

use App\Models\Team;
use App\Traits\HasStaticAttributes;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    use HasStaticAttributes;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->teams = factory(Team::class, 2)->withoutEvents()->create();
    }
}
