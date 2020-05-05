<?php

use App\Models\User;
use App\Traits\HasStaticAttributes;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    use HasStaticAttributes;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = app(User::class)->create([
            'name' => env('ADMIN_NAME'),
            'email' => env('ADMIN_EMAIL'),
            'password' => Hash::make(env('ADMIN_PASSWORD')),
        ]);

        $users = factory(User::class, 5)->create();

        $this->users = app(Collection::class)->merge([$admin, ...$users]);
    }
}
