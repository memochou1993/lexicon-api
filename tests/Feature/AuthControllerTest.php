<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
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
            ->assertOk()
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
            ->assertCreated()
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
            ->assertJsonValidationErrors([
                'email',
            ]);
    }

    /**
     * @return void
     */
    public function testLogout()
    {
        Sanctum::actingAs($this->user);

        $this->json('POST', 'api/auth/logout')
            ->assertNoContent();
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
            ->assertUnauthorized();
    }

    /**
     * @return void
     */
    public function testLogoutUnauthorized()
    {
        $this->json('POST', 'api/auth/logout')
            ->assertUnauthorized();
    }
}
