<?php

namespace Tests\Feature\Api\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TokenControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function testStore()
    {
        $user = $this->user;

        $this->json('POST', 'api/auth/tokens', [
            'email' => $user->email,
            'password' => 'password',
        ])
            ->assertOk()
            ->assertJsonStructure([
                'access_token',
            ]);

        $this->assertCount(1, $user->refresh()->tokens);
    }

    /**
     * @return void
     */
    public function testDestroy()
    {
        Sanctum::actingAs($this->user);

        $this->json('DELETE', 'api/auth/tokens')
            ->assertNoContent();
    }

    /**
     * @return void
     */
    public function testStoreUnauthorized()
    {
        $user = $this->user;

        $this->json('POST', 'api/auth/tokens', [
            'email' => $user->email,
            'password' => 'secret',
        ])
            ->assertUnauthorized();
    }

    /**
     * @return void
     */
    public function testDestroyUnauthorized()
    {
        $this->json('DELETE', 'api/auth/tokens')
            ->assertUnauthorized();
    }
}
