<?php

namespace Tests\Feature\Api\User;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class TeamControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function testIndex()
    {
        Sanctum::actingAs($this->user);

        /** @var Team $team */
        $team = factory(Team::class)->create();

        $this->json('GET', 'api/user/teams', [
            'relations' => 'users,projects,languages,forms',
        ])
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    [
                        'owner',
                        'users',
                        'projects',
                        'languages',
                        'forms',
                    ],
                ],
            ])
            ->assertJson([
                'data' => [
                    $team->toArray(),
                ],
            ]);
    }

    /**
     * @return void
     */
    public function testStore()
    {
        /** @var User $user */
        $user = Sanctum::actingAs($this->user);

        $data = factory(Team::class)->make()->toArray();

        $this->json('POST', 'api/user/teams', $data)
            ->assertCreated();

        $this->assertCount(1, $user->refresh()->teams);
    }

    /**
     * @return void
     */
    public function testStoreDuplicate()
    {
        /** @var User $user */
        $user = Sanctum::actingAs($this->user);

        factory(Team::class)->create([
            'name' => 'Unique Team',
        ]);

        $data = factory(Team::class)->make([
            'name' => 'Unique Team',
        ])->toArray();

        $this->json('POST', 'api/user/teams', $data)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors([
                'name',
            ]);

        $this->assertCount(1, $user->refresh()->teams);
    }
}
