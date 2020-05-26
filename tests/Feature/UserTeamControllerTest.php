<?php

namespace Tests\Feature;

use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserTeamControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function testIndex()
    {
        $user = Sanctum::actingAs($this->user);

        $team = $user->teams()->save(factory(Team::class)->make());

        $this->json('GET', 'api/user/teams', [
            'relations' => 'users,projects,languages,forms',
        ])
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    [
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
        $user = Sanctum::actingAs($this->user);

        $data = factory(Team::class)->make()->toArray();

        $this->json('POST', 'api/user/teams', $data)
            ->assertCreated()
            ->assertJson([
                'data' => $data,
            ]);

        $this->assertDatabaseHas('teams', $data);

        $this->assertCount(1, $user->teams);
    }

    /**
     * @return void
     */
    public function testStoreDuplicate()
    {
        $user = Sanctum::actingAs($this->user);

        $user->teams()->save(factory(Team::class)->make([
            'name' => 'Unique Team',
        ]));

        $data = factory(Team::class)->make([
            'name' => 'Unique Team',
        ])->toArray();

        $this->json('POST', 'api/user/teams', $data)
            ->assertJsonValidationErrors([
                'name',
            ]);

        $this->assertCount(1, $user->teams);
    }
}
