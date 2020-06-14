<?php

namespace Tests\Feature\Api\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function testStore()
    {
        $data = factory(User::class)->make([
            'email_verified_at' => null,
        ])->makeVisible('password')->toArray();

        $this->json('POST', 'api/auth/users', $data)
            ->assertCreated();
    }

    /**
     * @return void
     */
    public function testStoreDuplicate()
    {
        factory(User::class)->create([
            'email' => 'unique@email.com',
        ]);

        $data = factory(User::class)->make([
            'email' => 'unique@email.com',
        ])->makeVisible('password')->toArray();

        $this->json('POST', 'api/auth/users', $data)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors([
                'email',
            ]);
    }
}
