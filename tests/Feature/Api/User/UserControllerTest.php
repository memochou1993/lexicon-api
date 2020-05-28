<?php

namespace Tests\Feature\Api\User;

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
    public function testShow()
    {
        $user = Sanctum::actingAs($this->user);
        $user->roles()->save(factory(Role::class)->make());

        $this->json('GET', 'api/user', [
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
        Sanctum::actingAs($this->user);

        $data = factory(User::class)->make([
            'name' => 'New User',
        ])->toArray();

        $this->json('PATCH', 'api/user', $data)
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
        Sanctum::actingAs($this->user);

        $data = factory(User::class)->create()->toArray();

        $this->json('PATCH', 'api/user', $data)
            ->assertJsonValidationErrors([
                'email',
            ]);
    }
}
