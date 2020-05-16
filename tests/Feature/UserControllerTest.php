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
        $this->json('GET', 'api/users/'.$this->user->id, [
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
        $data = factory(User::class)->make([
            'name' => 'New User',
        ])->toArray();

        $this->json('PATCH', 'api/users/'.$this->user->id, $data)
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
        $user = factory(User::class)->create();

        $data = factory(User::class)->make([
            'email' => $user->email,
        ])->toArray();

        $this->json('PATCH', 'api/users/'.$this->user->id, $data)
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
        $this->json('DELETE', 'api/users/'.$this->user->id)
            ->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDeleted($this->user);
    }
}
