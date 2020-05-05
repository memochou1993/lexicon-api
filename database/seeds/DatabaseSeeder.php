<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            UserSeeder::class,
            TeamSeeder::class,
            ProjectSeeder::class,
            LanguageSeeder::class,
            FormSeeder::class,
            ModelHasUserSeeder::class,
            ModelHasLanguageSeeder::class,
            ModelHasFormSeeder::class,
        ]);
    }
}
