<?php

namespace Tests\Feature\Api;

use App\Enums\ErrorType;
use App\Enums\PermissionType;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserRoleControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function testAttach()
    {
        $user = Sanctum::actingAs($this->user, [PermissionType::USER_UPDATE]);

        $role = factory(Role::class)->create();

        $this->assertCount(0, $user->roles);

        $this->json('POST', 'api/users/'.$user->id.'/roles', [
            'role_ids' => $role->id,
        ])
            ->assertNoContent();

        $this->assertCount(1, $user->refresh()->roles);
    }

    /**
     * @return void
     */
    public function testDetach()
    {
        $user = Sanctum::actingAs($this->user, [PermissionType::USER_UPDATE]);

        $role = $user->roles()->save(factory(Role::class)->make());

        $this->assertCount(1, $user->roles);

        $this->json('DELETE', 'api/users/'.$user->id.'/roles/'.$role->id)
            ->assertNoContent();

        $this->assertCount(0, $user->refresh()->roles);
    }

    /**
     * @return void
     */
    public function testAttachWithoutPermission()
    {
        $user = Sanctum::actingAs($this->user);

        $response = $this->json('POST', 'api/users/'.$user->id.'/roles')
            ->assertForbidden();

        $this->assertEquals(
            ErrorType::PERMISSION_DENIED,
            $response->exception->getCode()
        );
    }

    /**
     * @return void
     */
    public function testDetachWithoutPermission()
    {
        $user = Sanctum::actingAs($this->user);

        $role = $user->roles()->save(factory(Role::class)->make());

        $response = $this->json('DELETE', 'api/users/'.$user->id.'/roles/'.$role->id)
            ->assertForbidden();

        $this->assertEquals(
            ErrorType::PERMISSION_DENIED,
            $response->exception->getCode()
        );
    }
}