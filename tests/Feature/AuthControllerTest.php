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
        $user = factory(User::class)->create();

        $response = $this->json('POST', 'api/auth/login', [
            'email' => $user->email,
            'password' => 'password'
        ]);

        $response
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'access_token',
            ]);

        $this->assertCount(1, $user->tokens);
    }

    /**
     * @return void
     */
    public function testLoginFail()
    {
        $user = factory(User::class)->make();

        $response = $this->json('POST', 'api/auth/login', [
            'email' => $user->email,
            'password' => 'password'
        ]);

        $response
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @return void
     */
    public function testUser()
    {
        $user = Sanctum::actingAs(factory(User::class)->create());

        $response = $this->json('GET', 'api/auth/user');

        $response
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'data' => $user->toArray(),
            ]);
    }

    /**
     * @return void
     */
    public function testUserFail()
    {
        $response = $this->json('GET', 'api/auth/user');

        $response
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @return void
     */
    public function testLogout()
    {
        $user = Sanctum::actingAs(factory(User::class)->create());

        $response = $this->json('POST', 'api/auth/logout');

        $response
            ->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertCount(0, $user->tokens);
    }

    /**
     * @return void
     */
    public function testLogoutFail()
    {
        $response = $this->json('POST', 'api/auth/logout');

        $response
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }
}
