<?php

namespace Tests;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Laravel\Sanctum\Sanctum;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * @return void
     */
    protected function setUpAdmin(): void
    {
        $this->seed([
            'PermissionSeeder',
            'RoleSeeder',
        ]);

        $admin = Role::where('name', config('permission.roles.admin.name'))
            ->first()
            ->users()
            ->save(factory(User::class)->make());

        Sanctum::actingAs($admin);
    }
}
