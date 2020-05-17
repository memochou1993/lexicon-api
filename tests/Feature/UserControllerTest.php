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
     * @return void
     */
    public function testIndex()
    {
        $admin = Sanctum::actingAs(factory(User::class)->create([
            'email' => env('ADMIN_EMAIL'),
        ]));

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
                    $admin->toArray(),
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
        $admin = Sanctum::actingAs(factory(User::class)->create([
            'email' => env('ADMIN_EMAIL'),
        ]));

        $this->json('GET', 'api/users/'.$admin->id, [
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
                'data' => $admin->toArray(),
            ]);
    }

    /**
     * @return void
     */
    public function testUserView()
    {
        $user = Sanctum::actingAs(factory(User::class)->create());

        $this->json('GET', 'api/users/'.$user->id, [
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
                'data' => $user->toArray(),
            ]);
    }

    /**
     * @return void
     */
    public function testViewForbidden()
    {
        Sanctum::actingAs(factory(User::class)->create());

        $guest = factory(User::class)->create();

        $this->json('GET', 'api/users/'.$guest->id)
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * @return void
     */
    public function testUpdate()
    {
        $admin = Sanctum::actingAs(factory(User::class)->create([
            'email' => env('ADMIN_EMAIL'),
        ]));

        $data = factory(User::class)->make([
            'name' => 'New User',
        ])->toArray();

        $this->json('PATCH', 'api/users/'.$admin->id, $data)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'data' => $data,
            ]);
    }

    /**
     * @return void
     */
    public function testUserUpdate()
    {
        $user = Sanctum::actingAs(factory(User::class)->create());

        $data = factory(User::class)->make([
            'name' => 'New User',
        ])->toArray();

        $this->json('PATCH', 'api/users/'.$user->id, $data)
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
        $admin = Sanctum::actingAs(factory(User::class)->create([
            'email' => env('ADMIN_EMAIL'),
        ]));

        $data = factory(User::class)->create()->toArray();

        $this->json('PATCH', 'api/users/'.$admin->id, $data)
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
        Sanctum::actingAs(factory(User::class)->create());

        $guest = factory(User::class)->create();

        $data = factory(User::class)->make()->toArray();

        $this->json('PATCH', 'api/users/'.$guest->id, $data)
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * @return void
     */
    public function testDestroy()
    {
        $admin = Sanctum::actingAs(factory(User::class)->create([
            'email' => env('ADMIN_EMAIL'),
        ]));

        $this->json('DELETE', 'api/users/'.$admin->id)
            ->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDeleted($admin);
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
