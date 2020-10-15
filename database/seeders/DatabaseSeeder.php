<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            TeamSeeder::class,
            ProjectSeeder::class,
            LanguageSeeder::class,
            FormSeeder::class,
            KeySeeder::class,
            ValueSeeder::class,
            HookSeeder::class,
            ModelHasUserSeeder::class,
            ModelHasLanguageSeeder::class,
            ModelHasFormSeeder::class,
        ]);
    }
}
