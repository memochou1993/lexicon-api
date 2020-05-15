<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var User
     */
    private $user;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->user = Sanctum::actingAs(factory(User::class)->create());
    }

    /**
     * @return void
     */
    public function testIndex()
    {
        $this->json('GET', 'api/users', [
            'relations' => 'teams,projects',
        ])
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data' => [
                    [
                        'teams',
                        'projects',
                    ],
                ],
            ])
            ->assertJson([
                'data' => User::all()->toArray(),
            ]);
    }

    /**
     * @return void
     */
    public function testShow()
    {
        $this->json('GET', 'api/users/1', [
            'relations' => 'teams,projects',
        ])
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data' => [
                    'teams',
                    'projects',
                ],
            ])
            ->assertJson([
                'data' => $this->user->toArray(),
            ]);
    }

    /**
     * @return void
     */
    public function testUpdate()
    {
        $user = factory(User::class)
            ->make([
                'name' => 'New User',
            ])
            ->toArray();

        $this->json('PATCH', 'api/users/1', $user)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'data' => $user,
            ]);
    }

    /**
     * @return void
     */
    public function testUpdateDuplicate()
    {
        factory(User::class)->create([
            'email' => 'unique@email.com',
        ]);

        $user = factory(User::class)
            ->make([
                'email' => 'new@email.com',
            ])
            ->toArray();

        $this->json('PATCH', 'api/users/1', $user)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'data' => $user,
            ]);

        $user = factory(User::class)->make([
            'email' => 'unique@email.com',
        ])->toArray();

        $this->json('PATCH', 'api/users/1', $user)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure([
                'errors' => [
                    'email',
                ],
            ]);
    }

    /**
     * @return void
     */
    public function testDestroy()
    {
        $this->json('DELETE', 'api/users/1')
            ->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDeleted($this->user);
    }
}
