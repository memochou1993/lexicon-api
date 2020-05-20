<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class RoleUserControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

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

    /**
     * @return void
     */
    public function testAttach()
    {
        $user = factory(User::class)->create();
        $role = factory(Role::class)->create();

        $this->assertCount(0, $role->users);

        $this->json('POST', 'api/roles/'.$role->id.'/users', [
            'user_ids' => $user->id,
        ])
            ->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertCount(1, $role->refresh()->users);
    }

    /**
     * @return void
     */
    public function testSync()
    {
        $user = factory(User::class)->create();
        $role = $user->roles()->save(factory(Role::class)->make());
        $role->users()->save(factory(User::class)->make());

        $this->assertCount(2, $role->users);

        $this->json('POST', 'api/roles/'.$role->id.'/users', [
            'user_ids' => $user->id,
            'sync' => true,
        ])
            ->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertCount(1, $role->refresh()->users);
    }

    /**
     * @return void
     */
    public function testAttachForbidden()
    {
        $user = Sanctum::actingAs(factory(User::class)->create());
        $role = factory(Role::class)->create();

        $this->json('POST', 'api/roles/'.$role->id.'/users', [
            'user_ids' => $user->id,
        ])
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * @return void
     */
    public function testDetach()
    {
        $user = factory(User::class)->create();
        $role = $user->roles()->save(factory(Role::class)->make());

        $this->assertCount(1, $role->users);

        $this->json('DELETE', 'api/roles/'.$role->id.'/users/'.$user->id)
            ->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertCount(0, $role->refresh()->users);
    }

    /**
     * @return void
     */
    public function testDetachForbidden()
    {
        $user = Sanctum::actingAs(factory(User::class)->create());
        $role = $user->roles()->save(factory(Role::class)->make());

        $this->json('DELETE', 'api/roles/'.$role->id.'/users/'.$user->id)
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }
}
