<?php

namespace Tests\Feature\Api\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
        /** @var User $user */
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
        /** @var User $user */
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
