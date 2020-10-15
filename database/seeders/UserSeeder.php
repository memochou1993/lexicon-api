<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Traits\HasStaticAttributes;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    use HasStaticAttributes;

    public const AMOUNT = 5;

    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        $users = collect(config('permission.roles'))
            ->map(function ($item, $index) {
                /** @var User $user */
                $user = User::query()->create([
                    'name' => $item['name'],
                    'email' => $index.'@email.com',
                    'password' => 'password',
                ]);

                $user->roles()->attach(
                    Role::query()->where('name', $item['name'])->first()
                );

                return $user;
            })
            ->merge(
                User::factory()->count(self::AMOUNT)->create()
            );

        $this->set('users', $users);
    }
}
