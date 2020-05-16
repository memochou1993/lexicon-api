<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class TeamProjectControllerTest extends TestCase
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
        $team->projects()->save(factory(Project::class)->make());

        $this->json('GET', 'api/teams/1/projects', [
            'relations' => 'users,languages',
        ])
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data' => [
                    [
                        'users',
                        'languages',
                    ],
                ],
            ])
            ->assertJson([
                'data' => $team->projects->toArray(),
            ]);
    }

    /**
     * @return void
     */
    public function testViewAllForbidden()
    {
        $guest = factory(User::class)->create();
        $team = $guest->teams()->save(factory(Team::class)->make());
        $team->projects()->save(factory(Project::class)->make())->toArray();

        $this->json('GET', 'api/teams/1/projects')
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * @return void
     */
    public function testStore()
    {
        $team = $this->user->teams()->save(factory(Team::class)->make());

        $project = factory(Project::class)->make()->toArray();

        $this->json('POST', 'api/teams/1/projects', $project)
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJson([
                'data' => $project,
            ]);

        $this->assertDatabaseHas('projects', $project);

        $this->assertCount(1, $team->projects);
    }

    /**
     * @return void
     */
    public function testStoreDuplicate()
    {
        $team = $this->user->teams()->save(factory(Team::class)->make());
        $team->projects()->save(factory(Project::class)->make([
            'name' => 'Unique Project',
        ]));

        $project = factory(Project::class)
            ->make([
                'name' => 'Unique Project',
            ])
            ->toArray();

        $this->json('POST', 'api/teams/1/projects', $project)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure([
                'errors' => [
                    'name',
                ],
            ]);

        $this->assertCount(1, $team->projects);
    }

    /**
     * @return void
     */
    public function testCreateForbidden()
    {
        $guest = factory(User::class)->create();
        $team = $guest->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->make())->toArray();

        $this->json('POST', 'api/teams/1/projects', $project)
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }
}
