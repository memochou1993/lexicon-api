<?php

use App\Models\Role;
use App\Traits\HasStaticAttributes;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    use HasStaticAttributes;

    public const DATA_AMOUNT = 3;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = factory(Role::class, self::DATA_AMOUNT)->withoutEvents()->create();

        $this->set('roles', $roles);
    }
}
