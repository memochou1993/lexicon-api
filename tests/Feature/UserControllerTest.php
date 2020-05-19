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
                'data' => [
                    $this->admin->toArray(),
                ],
            ]);
    }

    /**
     * @return void
     */
    public function testViewAllForbidden()
    {
        Sanctum::actingAs(factory(User::class)->create());

        $this->json('GET', 'api/users')
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * @return void
     */
    public function testShow()
    {
        $this->json('GET', 'api/users/'.$this->admin->id, [
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
                'data' => $this->admin->toArray(),
            ]);
    }

    /**
     * @return void
     */
    public function testViewForbidden()
    {
        $guest = Sanctum::actingAs(factory(User::class)->create());

        $this->json('GET', 'api/users/'.$guest->id)
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * @return void
     */
    public function testUpdate()
    {
        $data = factory(User::class)->make([
            'name' => 'New User',
        ])->toArray();

        $this->json('PATCH', 'api/users/'.$this->admin->id, $data)
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
        $data = factory(User::class)->create()->toArray();

        $this->json('PATCH', 'api/users/'.$this->admin->id, $data)
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
    public function testUpdateForbidden()
    {
        $guest = Sanctum::actingAs(factory(User::class)->create());

        $this->json('PATCH', 'api/users/'.$guest->id)
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * @return void
     */
    public function testDestroy()
    {
        $user = factory(User::class)->create();

        $this->json('DELETE', 'api/users/'.$user->id)
            ->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDeleted($user);
    }

    /**
     * @return void
     */
    public function testDeleteForbidden()
    {
        $user = Sanctum::actingAs(factory(User::class)->create());

        $this->json('DELETE', 'api/users/'.$user->id)
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }
}
