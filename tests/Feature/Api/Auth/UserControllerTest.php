<?php

namespace Tests\Feature\Api\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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
        ])->makeVisible('password');

        $this->json('POST', 'api/auth/users', $data->toArray())
            ->assertCreated()
            ->assertJson([
                'data' => $data->makeHidden('password')->toArray(),
            ]);
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
        ])->makeVisible('password');

        $this->json('POST', 'api/auth/users', $data->toArray())
            ->assertJsonValidationErrors([
                'email',
            ]);
    }
}
