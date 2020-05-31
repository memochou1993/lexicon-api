<?php

namespace Tests\Feature\Api;

use App\Enums\ErrorType;
use App\Enums\PermissionType;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function testIndex()
    {
        $user = Sanctum::actingAs($this->user, [PermissionType::USER_VIEW_ANY]);
        $user->roles()->save(factory(Role::class)->make());

        $this->json('GET', 'api/users', [
            'relations' => 'roles,roles.permissions,teams,projects',
        ])
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    [
                        'roles' => [
                            [
                                'permissions',
                            ],
                        ],
                        'teams',
                        'projects',
                    ],
                ],
            ]);
    }

    /**
     * @return void
     */
    public function testShow()
    {
        $user = Sanctum::actingAs($this->user, [PermissionType::USER_VIEW]);
        $user->roles()->save(factory(Role::class)->make());

        $this->json('GET', 'api/users/'.$user->id, [
            'relations' => 'roles,roles.permissions,teams,projects',
        ])
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'roles' => [
                        [
                            'permissions',
                        ],
                    ],
                    'teams',
                    'projects',
                ],
            ])
            ->assertJson([
                'data' => $user->toArray(),
            ]);
    }

    /**
     * @return void
     */
    public function testUpdate()
    {
        $user = Sanctum::actingAs($this->user, [PermissionType::USER_UPDATE]);

        $role_ids = factory(Role::class, 2)->create()->pluck('id')->toArray();

        $data = factory(User::class)->make([
            'name' => 'New User',
            'role_ids' => $role_ids,
        ]);

        $this->json('PATCH', 'api/users/'.$user->id, $data->toArray())
            ->assertOk()
            ->assertJson([
                'data' => $data->makeHidden('role_ids')->toArray(),
            ]);

        $this->assertCount(count($role_ids), $user->refresh()->roles);
    }

    /**
     * @return void
     */
    public function testUpdateDuplicate()
    {
        $user = Sanctum::actingAs($this->user, [PermissionType::USER_UPDATE]);

        $data = factory(User::class)->create()->toArray();

        $this->json('PATCH', 'api/users/'.$user->id, $data)
            ->assertJsonValidationErrors([
                'email',
            ]);
    }

    /**
     * @return void
     */
    public function testDestroy()
    {
        Sanctum::actingAs($this->user, [PermissionType::USER_DELETE]);

        $user = factory(User::class)->create();

        $this->json('DELETE', 'api/users/'.$user->id)
            ->assertNoContent();

        $this->assertDeleted($user);
    }

    /**
     * @return void
     */
    public function testViewAllWithoutPermission()
    {
        Sanctum::actingAs($this->user);

        $response = $this->json('GET', 'api/users')
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
        $user = Sanctum::actingAs($this->user);

        $response = $this->json('GET', 'api/users/'.$user->id)
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
        $user = Sanctum::actingAs($this->user);

        $response = $this->json('PATCH', 'api/users/'.$user->id)
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

        $user = factory(User::class)->create();

        $response = $this->json('DELETE', 'api/users/'.$user->id)
            ->assertForbidden();

        $this->assertEquals(
            ErrorType::PERMISSION_DENIED,
            $response->exception->getCode()
        );
    }
}
