<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class RoleControllerTest extends TestCase
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
    public function testIndex()
    {
        $this->json('GET', 'api/roles', [
            'relations' => 'users',
        ])
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data' => [
                    [
                        'users',
                    ],
                ],
            ]);
    }

    /**
     * @return void
     */
    public function testViewAllForbidden()
    {
        Sanctum::actingAs(factory(User::class)->create());

        $this->json('GET', 'api/roles')
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * @return void
     */
    public function testStore()
    {
        $data = factory(Role::class)->make()->toArray();

        $this->json('POST', 'api/roles', $data)
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJson([
                'data' => $data,
            ]);
    }

    /**
     * @return void
     */
    public function testStoreDuplicate()
    {
        factory(Role::class)->create([
            'name' => 'Unique Role',
        ]);

        $data = factory(Role::class)->make([
            'name' => 'Unique Role',
        ])->toArray();

        $this->json('POST', 'api/roles', $data)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure([
                'errors' => [
                    'name',
                ],
            ]);
    }

    /**
     * @return void
     */
    public function testCreateForbidden()
    {
        Sanctum::actingAs(factory(User::class)->create());

        $data = factory(Role::class)->make()->toArray();

        $this->json('POST', 'api/roles', $data)
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * @return void
     */
    public function testShow()
    {
        $role = factory(Role::class)->create();

        $this->json('GET', 'api/roles/'.$role->id, [
            'relations' => 'users',
        ])
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'data' => $role->toArray(),
            ]);
    }

    /**
     * @return void
     */
    public function testViewForbidden()
    {
        Sanctum::actingAs(factory(User::class)->create());

        $role = factory(Role::class)->make();

        $this->json('GET', 'api/roles/'.$role->id)
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * @return void
     */
    public function testUpdate()
    {
        $role = factory(Role::class)->create();

        $data = factory(Role::class)->make([
            'name' => 'New Role',
        ])->toArray();

        $this->json('PATCH', 'api/roles/'.$role->id, $data)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'data' => $data,
            ]);
    }

    /**
     * @return void
     */
    public function testUpdateDuplicate()
    {
        $role = factory(Role::class)->create();

        $data = factory(Role::class)->create()->toArray();

        $this->json('PATCH', 'api/roles/'.$role->id, $data)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure([
                'errors' => [
                    'name',
                ],
            ]);
    }

    /**
     * @return void
     */
    public function testUpdateForbidden()
    {
        Sanctum::actingAs(factory(User::class)->create());

        $role = factory(Role::class)->create();

        $this->json('PATCH', 'api/roles/'.$role->id)
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * @return void
     */
    public function testDestroy()
    {
        $user = factory(User::class)->create();
        $role = $user->roles()->save(factory(Role::class)->make());

        $this->json('DELETE', 'api/roles/'.$role->id)
            ->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseMissing('model_has_users', [
            'user_id' => $user->id,
            'model_type' => 'role',
            'model_id' => $role->id,
        ]);
    }

    /**
     * @return void
     */
    public function testDeleteForbidden()
    {
        Sanctum::actingAs(factory(User::class)->create());

        $role = factory(Role::class)->create();

        $this->json('DELETE', 'api/roles/'.$role->id)
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }
}
