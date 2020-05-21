<?php

namespace Tests\Feature;

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
        Sanctum::actingAs($this->user, ['view-role']);

        factory(Role::class)->create();

        $this->json('GET', 'api/roles', [
            'relations' => 'users',
        ])
            ->assertOk()
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
    public function testStore()
    {
        Sanctum::actingAs($this->user, ['create-role']);

        $data = factory(Role::class)->make()->toArray();

        $this->json('POST', 'api/roles', $data)
            ->assertCreated()
            ->assertJson([
                'data' => $data,
            ]);
    }

    /**
     * @return void
     */
    public function testStoreDuplicate()
    {
        Sanctum::actingAs($this->user, ['create-role']);

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
        Sanctum::actingAs($this->user, ['view-role']);

        $role = factory(Role::class)->create();

        $this->json('GET', 'api/roles/'.$role->id, [
            'relations' => 'users',
        ])
            ->assertOk()
            ->assertJson([
                'data' => $role->toArray(),
            ]);
    }

    /**
     * @return void
     */
    public function testUpdate()
    {
        Sanctum::actingAs($this->user, ['update-role']);

        $role = factory(Role::class)->create();

        $data = factory(Role::class)->make([
            'name' => 'New Role',
        ])->toArray();

        $this->json('PATCH', 'api/roles/'.$role->id, $data)
            ->assertOk()
            ->assertJson([
                'data' => $data,
            ]);
    }

    /**
     * @return void
     */
    public function testUpdateDuplicate()
    {
        Sanctum::actingAs($this->user, ['update-role']);

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
        $user = Sanctum::actingAs($this->user, ['delete-role']);

        $role = $user->roles()->save(factory(Role::class)->make());

        $this->json('DELETE', 'api/roles/'.$role->id)
            ->assertNoContent();

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

        $this->json('GET', 'api/roles')
            ->assertForbidden();
    }

    /**
     * @return void
     */
    public function testCreateWithoutPermission()
    {
        Sanctum::actingAs($this->user);

        $data = factory(Role::class)->make()->toArray();

        $this->json('POST', 'api/roles', $data)
            ->assertForbidden();
    }

    /**
     * @return void
     */
    public function testViewWithoutPermission()
    {
        Sanctum::actingAs($this->user);

        $role = factory(Role::class)->create();

        $this->json('GET', 'api/roles/'.$role->id)
            ->assertForbidden();
    }

    /**
     * @return void
     */
    public function testUpdateWithoutPermission()
    {
        Sanctum::actingAs($this->user);

        $role = factory(Role::class)->create();

        $this->json('PATCH', 'api/roles/'.$role->id)
            ->assertForbidden();
    }

    /**
     * @return void
     */
    public function testDeleteWithoutPermission()
    {
        Sanctum::actingAs($this->user);

        $role = factory(Role::class)->create();

        $this->json('DELETE', 'api/roles/'.$role->id)
            ->assertForbidden();
    }
}
