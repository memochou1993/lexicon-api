<?php

namespace Tests\Feature;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class TeamControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var User
     */
    private $user;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->user = Sanctum::actingAs(factory(User::class)->create());
    }

    /**
     * @return void
     */
    public function testIndex()
    {
        $team = $this->user->teams()->save(factory(Team::class)->make());

        $this->json('GET', 'api/teams', [
            'relations' => 'users,projects,languages,forms',
        ])
            ->assertStatus(Response::HTTP_OK)
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
        $team = factory(Team::class)->make()->toArray();

        $this->json('POST', 'api/teams', $team)
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJson([
                'data' => $team,
            ]);

        $this->assertDatabaseHas('teams', $team);

        $this->assertCount(1, $this->user->teams);
    }

    /**
     * @return void
     */
    public function testStoreDuplicate()
    {
        $this->user->teams()->save(factory(Team::class)->make([
            'name' => 'Unique Team',
        ]));

        $team = factory(Team::class)->make([
            'name' => 'Unique Team',
        ])->toArray();

        $this->json('POST', 'api/teams', $team)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure([
                'errors' => [
                    'name',
                ],
            ]);

        $this->assertCount(1, $this->user->teams);
    }

    /**
     * @return void
     */
    public function testShow()
    {
        // TODO
    }

    /**
     * @return void
     */
    public function testUpdate()
    {
        // TODO
    }

    /**
     * @return void
     */
    public function testUpdateDuplicate()
    {
        // TODO
    }

    /**
     * @return void
     */
    public function testDestroy()
    {
        // TODO
    }
}
