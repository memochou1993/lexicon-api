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
     * @var User
     */
    private $admin;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->admin = Sanctum::actingAs(factory(User::class)->create([
            'email' => env('ADMIN_EMAIL'),
        ]));
    }

    /**
     * @return void
     */
    public function testIndex()
    {
        $role = $this->admin->roles()->save(factory(Role::class)->make());

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
            ])
            ->assertJson([
                'data' => [
                    $role->toArray(),
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
        $this->admin->roles()->save(factory(Role::class)->make([
            'name' => 'Unique Role',
        ]));

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
        $role = $this->admin->roles()->save(factory(Role::class)->make());

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
        $guest = Sanctum::actingAs(factory(User::class)->create());
        $role = $guest->roles()->save(factory(Role::class)->make());

        $this->json('GET', 'api/roles/'.$role->id)
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * @return void
     */
    public function testUpdate()
    {
        $role = $this->admin->roles()->save(factory(Role::class)->make());

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
        $role = $this->admin->roles()->save(factory(Role::class)->make());

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
        $guest = Sanctum::actingAs(factory(User::class)->create());
        $role = $guest->roles()->save(factory(Role::class)->make());

        $this->json('PATCH', 'api/roles/'.$role->id)
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * @return void
     */
    public function testDestroy()
    {
        $role = $this->admin->roles()->save(factory(Role::class)->make());

        $this->json('DELETE', 'api/roles/'.$role->id)
            ->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseMissing('model_has_users', [
            'user_id' => $this->admin->id,
            'model_type' => 'role',
            'model_id' => $role->id,
        ]);
    }

    /**
     * @return void
     */
    public function testDeleteForbidden()
    {
        $guest = Sanctum::actingAs(factory(User::class)->create());

        $role = $guest->roles()->save(factory(Role::class)->make());

        $this->json('DELETE', 'api/roles/'.$role->id)
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }
}
