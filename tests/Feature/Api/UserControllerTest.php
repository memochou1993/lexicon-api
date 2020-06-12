<?php

namespace Tests\Feature\Api;

use App\Enums\ErrorType;
use App\Enums\PermissionType;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function testIndex()
    {
        /** @var User $user */
        $user = Sanctum::actingAs($this->user, [
            PermissionType::USER_VIEW_ANY,
        ]);
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
        /** @var User $user */
        $user = Sanctum::actingAs($this->user, [
            PermissionType::USER_VIEW,
        ]);
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
        /** @var User $user */
        $user = Sanctum::actingAs($this->user, [
            PermissionType::USER_UPDATE,
        ]);

        /** @var Role $role */
        $role = factory(Role::class)->create();

        $data = factory(User::class)->make([
            'name' => 'New User',
            'role_ids' => $role->id,
        ]);

        $this->json('PATCH', 'api/users/'.$user->id, $data->toArray())
            ->assertOk()
            ->assertJson([
                'data' => $data->makeHidden('role_ids')->toArray(),
            ]);

        $this->assertCount(1, $user->refresh()->roles);
    }

    /**
     * @return void
     */
    public function testUpdateDuplicate()
    {
        /** @var User $user */
        $user = Sanctum::actingAs($this->user, [
            PermissionType::USER_UPDATE,
        ]);

        $data = factory(User::class)->create()->toArray();

        $this->json('PATCH', 'api/users/'.$user->id, $data)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors([
                'email',
            ]);
    }

    /**
     * @return void
     */
    public function testDestroy()
    {
        Sanctum::actingAs($this->user, [
            PermissionType::USER_DELETE,
        ]);

        /** @var User $user */
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
        /** @var User $user */
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
        /** @var User $user */
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

        /** @var User $user */
        $user = factory(User::class)->create();

        $response = $this->json('DELETE', 'api/users/'.$user->id)
            ->assertForbidden();

        $this->assertEquals(
            ErrorType::PERMISSION_DENIED,
            $response->exception->getCode()
        );
    }
}
