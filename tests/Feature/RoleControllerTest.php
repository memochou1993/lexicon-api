<?php

namespace Tests\Feature;

use App\Enums\ErrorType;
use App\Enums\PermissionType;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RoleControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function testIndex()
    {
        Sanctum::actingAs($this->user, [PermissionType::ROLE_VIEW_ANY]);

        factory(Role::class)->create();

        $this->json('GET', 'api/roles', [
            'relations' => 'users,permissions',
        ])
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    [
                        'users',
                        'permissions',
                    ],
                ],
            ]);
    }

    /**
     * @return void
     */
    public function testStore()
    {
        Sanctum::actingAs($this->user, [PermissionType::ROLE_CREATE]);

        $permission_ids = factory(Permission::class, 2)->create()->pluck('id')->toArray();

        $data = factory(Role::class)->make([
            'permission_ids' => $permission_ids,
        ]);

        $response = $this->json('POST', 'api/roles', $data->toArray())
            ->assertCreated()
            ->assertJson([
                'data' => $data->makeHidden('permission_ids')->toArray(),
            ]);

        $this->assertDatabaseHas('roles', $data->toArray());

        $this->assertCount(
            count($permission_ids),
            Role::find(json_decode($response->getContent())->data->id)->permissions
        );
    }

    /**
     * @return void
     */
    public function testStoreDuplicate()
    {
        Sanctum::actingAs($this->user, [PermissionType::ROLE_CREATE]);

        factory(Role::class)->create([
            'name' => 'Unique Role',
        ]);

        $data = factory(Role::class)->make([
            'name' => 'Unique Role',
        ])->toArray();

        $this->json('POST', 'api/roles', $data)
            ->assertJsonValidationErrors([
                'name',
            ]);
    }

    /**
     * @return void
     */
    public function testShow()
    {
        Sanctum::actingAs($this->user, [PermissionType::ROLE_VIEW]);

        $role = factory(Role::class)->create();

        $this->json('GET', 'api/roles/'.$role->id, [
            'relations' => 'users,permissions',
        ])
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'users',
                    'permissions',
                ],
            ])
            ->assertJson([
                'data' => $role->toArray(),
            ]);
    }

    /**
     * @return void
     */
    public function testUpdate()
    {
        Sanctum::actingAs($this->user, [PermissionType::ROLE_UPDATE]);

        $permission_ids = factory(Permission::class, 2)->create()->pluck('id')->toArray();

        $role = factory(Role::class)->create();

        $data = factory(Role::class)->make([
            'name' => 'New Role',
            'permission_ids' => $permission_ids,
        ]);

        $response = $this->json('PATCH', 'api/roles/'.$role->id, $data->toArray())
            ->assertOk()
            ->assertJson([
                'data' => $data->makeHidden('permission_ids')->toArray(),
            ]);

        $this->assertDatabaseHas('roles', $data->toArray());

        $this->assertCount(
            count($permission_ids),
            Role::find(json_decode($response->getContent())->data->id)->permissions
        );
    }

    /**
     * @return void
     */
    public function testUpdateDuplicate()
    {
        Sanctum::actingAs($this->user, [PermissionType::ROLE_UPDATE]);

        $role = factory(Role::class)->create();

        $data = factory(Role::class)->create()->toArray();

        $this->json('PATCH', 'api/roles/'.$role->id, $data)
            ->assertJsonValidationErrors([
                'name',
            ]);
    }

    /**
     * @return void
     */
    public function testDestroy()
    {
        $user = Sanctum::actingAs($this->user, [PermissionType::ROLE_DELETE]);

        $role = $user->roles()->save(factory(Role::class)->make());

        $this->json('DELETE', 'api/roles/'.$role->id)
            ->assertNoContent();

        $this->assertDeleted($role);

        $this->assertDatabaseMissing('model_has_users', [
            'user_id' => $user->id,
            'model_type' => 'role',
            'model_id' => $role->id,
        ]);
    }

    /**
     * @return void
     */
    public function testViewAllWithoutPermission()
    {
        Sanctum::actingAs($this->user);

        $response = $this->json('GET', 'api/roles')
            ->assertForbidden();

        $this->assertEquals(
            ErrorType::PERMISSION_DENIED,
            $response->exception->getCode()
        );
    }

    /**
     * @return void
     */
    public function testCreateWithoutPermission()
    {
        Sanctum::actingAs($this->user);

        $data = factory(Role::class)->make()->toArray();

        $response = $this->json('POST', 'api/roles', $data)
            ->assertForbidden();

        $this->assertEquals(
            ErrorType::PERMISSION_DENIED,
            $response->exception->getCode()
        );
    }

    /**
     * @return void
     */
    public function testViewWithoutPermission()
    {
        Sanctum::actingAs($this->user);

        $role = factory(Role::class)->create();

        $response = $this->json('GET', 'api/roles/'.$role->id)
            ->assertForbidden();

        $this->assertEquals(
            ErrorType::PERMISSION_DENIED,
            $response->exception->getCode()
        );
    }

    /**
     * @return void
     */
    public function testUpdateWithoutPermission()
    {
        Sanctum::actingAs($this->user);

        $role = factory(Role::class)->create();

        $response = $this->json('PATCH', 'api/roles/'.$role->id)
            ->assertForbidden();

        $this->assertEquals(
            ErrorType::PERMISSION_DENIED,
            $response->exception->getCode()
        );
    }

    /**
     * @return void
     */
    public function testDeleteWithoutPermission()
    {
        Sanctum::actingAs($this->user);

        $role = factory(Role::class)->create();

        $response = $this->json('DELETE', 'api/roles/'.$role->id)
            ->assertForbidden();

        $this->assertEquals(
            ErrorType::PERMISSION_DENIED,
            $response->exception->getCode()
        );
    }
}
