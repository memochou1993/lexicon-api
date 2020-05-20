<?php

namespace Tests;

use App\Models\Role;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Laravel\Sanctum\Sanctum;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * @param  string  $role
     * @return UserContract
     */
    protected function setUpUser(string $role): UserContract
    {
        $this->seed([
            'PermissionSeeder',
            'RoleSeeder',
        ]);

        $user = Role::where('name', config('permission.roles.'.$role.'.name'))
            ->first()
            ->users()
            ->save(factory(User::class)->make());

        return Sanctum::actingAs($user);
    }
}
