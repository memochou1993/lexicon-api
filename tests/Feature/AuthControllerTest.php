<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function testLogin()
    {
        $user = $this->user;

        $this->json('POST', 'api/auth/login', [
            'email' => $user->email,
            'password' => 'password',
        ])
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'access_token',
            ]);

        $this->assertCount(1, $user->tokens);
    }

    /**
     * @return void
     */
    public function testRegister()
    {
        $data = factory(User::class)->make([
            'email_verified_at' => null,
        ])->makeVisible('password');

        $this->json('POST', 'api/auth/register', $data->toArray())
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJson([
                'data' => $data->makeHidden('password')->toArray(),
            ]);
    }

    /**
     * @return void
     */
    public function testRegisterDuplicate()
    {
        factory(User::class)->create([
            'email' => 'unique@email.com',
        ]);

        $data = factory(User::class)->make([
            'email' => 'unique@email.com',
        ])->makeVisible('password');

        $this->json('POST', 'api/auth/register', $data->toArray())
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
    public function testGetUser()
    {
        $user = Sanctum::actingAs($this->user);

        $this->json('GET', 'api/auth/user')
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'data' => $user->toArray(),
            ]);
    }

    /**
     * @return void
     */
    public function testUpdateUser()
    {
        Sanctum::actingAs($this->user);

        $data = factory(User::class)->make([
            'name' => 'New User',
        ])->toArray();

        $this->json('PATCH', 'api/auth/user', $data)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'data' => $data,
            ]);
    }

    /**
     * @return void
     */
    public function testUpdateUserDuplicate()
    {
        Sanctum::actingAs($this->user);

        $data = factory(User::class)->create()->toArray();

        $this->json('PATCH', 'api/auth/user', $data)
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
    public function testLogout()
    {
        Sanctum::actingAs($this->user);

        $this->json('POST', 'api/auth/logout')
            ->assertStatus(Response::HTTP_NO_CONTENT);
    }

    /**
     * @return void
     */
    public function testLoginUnauthorized()
    {
        $user = $this->user;

        $this->json('POST', 'api/auth/login', [
            'email' => $user->email,
            'password' => 'secret',
        ])
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @return void
     */
    public function testGetUserUnauthorized()
    {
        $this->json('GET', 'api/auth/user')
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @return void
     */
    public function testUpdateUserUnauthorized()
    {
        $this->json('PATCH', 'api/auth/user')
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @return void
     */
    public function testLogoutUnauthorized()
    {
        $this->json('POST', 'api/auth/logout')
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }
}
