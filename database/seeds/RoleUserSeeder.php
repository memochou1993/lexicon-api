<?php

use Illuminate\Database\Seeder;

class RoleUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = app(RoleSeeder::class)->roles;

        app(UserSeeder::class)->users->each(function ($user) use ($roles) {
            $user->roles()->saveMany($roles);
        });
    }
}
