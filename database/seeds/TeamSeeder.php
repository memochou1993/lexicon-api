<?php

use App\Models\Team;
use App\Traits\HasStaticAttributes;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
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
        $teams = factory(Team::class, self::DATA_AMOUNT)->create();

        $this->set('teams', $teams);
    }
}
