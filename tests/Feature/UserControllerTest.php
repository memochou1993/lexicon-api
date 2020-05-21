<?php

namespace Tests\Feature;

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
        Sanctum::actingAs($this->user, ['view-user']);

        $this->json('GET', 'api/users', [
            'relations' => 'teams,projects',
        ])
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    [
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
        $user = Sanctum::actingAs($this->user, ['view-user']);

        $this->json('GET', 'api/users/'.$user->id, [
            'relations' => 'teams,projects',
        ])
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
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
        $user = Sanctum::actingAs($this->user, ['update-user']);

        $data = factory(User::class)->make([
            'name' => 'New User',
        ])->toArray();

        $this->json('PATCH', 'api/users/'.$user->id, $data)
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
        $user = Sanctum::actingAs($this->user, ['update-user']);

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
        Sanctum::actingAs($this->user, ['delete-user']);

        $user = factory(User::class)->create();

        $this->json('DELETE', 'api/users/'.$user->id)
            ->assertNoContent();

        $this->assertDeleted($user);
    }

    /**
     * @return void
     */
    public function testViewAllForbidden()
    {
        Sanctum::actingAs($this->user);

        $this->json('GET', 'api/users')
            ->assertForbidden();
    }

    /**
     * @return void
     */
    public function testViewForbidden()
    {
        $user = Sanctum::actingAs($this->user);

        $this->json('GET', 'api/users/'.$user->id)
            ->assertForbidden();
    }

    /**
     * @return void
     */
    public function testUpdateForbidden()
    {
        $user = Sanctum::actingAs($this->user);

        $this->json('PATCH', 'api/users/'.$user->id)
            ->assertForbidden();
    }

    /**
     * @return void
     */
    public function testDeleteForbidden()
    {
        Sanctum::actingAs($this->user);

        $user = factory(User::class)->create();

        $this->json('DELETE', 'api/users/'.$user->id)
            ->assertForbidden();
    }
}
