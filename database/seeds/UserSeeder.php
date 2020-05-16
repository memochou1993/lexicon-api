<?php

use App\Models\User;
use App\Traits\HasStaticAttributes;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
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
        $admin = app(User::class)->create([
            'name' => env('ADMIN_NAME'),
            'email' => env('ADMIN_EMAIL'),
            'password' => env('ADMIN_PASSWORD'),
        ]);

        $users = factory(User::class, self::DATA_AMOUNT)->withoutEvents()->create();

        $this->set('users', app(Collection::class)->merge([$admin, ...$users]));
    }
}
