<?php

namespace Database\Seeders;

use App\Models\Team;
use App\Traits\HasStaticAttributes;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    use HasStaticAttributes;

    public const AMOUNT = 2;

    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        $teams = Team::factory()->count(self::AMOUNT)->create();

        $this->set('teams', $teams);
    }
}
