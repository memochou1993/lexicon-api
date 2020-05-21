<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class UserRoleControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->actingAsRole('admin');
    }

    /**
     * @return void
     */
    public function testAttach()
    {
        $user = factory(User::class)->create();
        $role = factory(Role::class)->create();

        $this->assertCount(0, $user->roles);

        $this->json('POST', 'api/users/'.$user->id.'/roles', [
            'role_ids' => $role->id,
        ])
            ->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertCount(1, $user->refresh()->roles);
    }

    /**
     * @return void
     */
    public function testSync()
    {
        $user = factory(User::class)->create();
        $user->roles()->saveMany(factory(Role::class, 2)->make());

        $this->assertCount(2, $user->roles);

        $this->json('POST', 'api/users/'.$user->id.'/roles', [
            'role_ids' => $user->roles()->first()->id,
            'sync' => true,
        ])
            ->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertCount(1, $user->refresh()->roles);
    }

    /**
     * @return void
     */
    public function testAttachForbidden()
    {
        $user = Sanctum::actingAs(factory(User::class)->create());
        $role = factory(Role::class)->create();

        $this->json('POST', 'api/users/'.$user->id.'/roles', [
            'role_ids' => $role->id,
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

        $this->assertCount(1, $user->roles);

        $this->json('DELETE', 'api/users/'.$user->id.'/roles/'.$role->id)
            ->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertCount(0, $user->refresh()->roles);
    }

    /**
     * @return void
     */
    public function testDetachForbidden()
    {
        $user = Sanctum::actingAs(factory(User::class)->create());
        $role = $user->roles()->save(factory(Role::class)->make());

        $this->json('DELETE', 'api/users/'.$user->id.'/roles/'.$role->id)
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }
}
