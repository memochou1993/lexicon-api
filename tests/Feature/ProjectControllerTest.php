<?php

namespace Tests\Feature;

use App\Models\Language;
use App\Models\Project;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ProjectControllerTest extends TestCase
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

        $this->json('GET', 'api/projects', [
            'team_id' => $team->id,
            'relations' => 'users,team,languages,keys',
        ])
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data' => [
                    [
                        'users',
                        'team',
                        'languages',
                        'keys',
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
    public function testStore()
    {
        $team = $this->user->teams()->save(factory(Team::class)->make());
        $project = factory(Project::class)->make();

        $project->team()->associate($team->id)->makeVisible('team_id');

        $this->json('POST', 'api/projects', $project->toArray())
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJson([
                'data' => $project->makeHidden('team_id')->toArray(),
            ]);

        $this->assertDatabaseHas('projects', $project->toArray());
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
            ->makeVisible('team_id')
            ->team()
            ->associate($team->id);

        $this->json('POST', 'api/projects', $project->toArray())
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $this->assertCount(1, $team->projects);
    }

    /**
     * @return void
     */
    public function testShow()
    {
        $team = $this->user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->make());

        $this->json('GET', 'api/projects/1', [
            'relations' => 'users,team,languages,keys,values',
        ])
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data' => [
                    'users',
                    'team',
                    'languages',
                    'keys',
                    'values',
                ],
            ])
            ->assertJson([
                'data' => $project->toArray(),
            ]);
    }

    /**
     * @return void
     */
    public function testUpdate()
    {
        $team = $this->user->teams()->save(factory(Team::class)->make());
        $team->projects()->save(factory(Project::class)->make());

        $project = factory(Project::class)->make([
            'name' => 'New Project',
        ])->toArray();

        $this->json('PATCH', 'api/projects/1', $project)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'data' => $project,
            ]);

        $this->assertDatabaseHas('projects', $project);
    }

    /**
     * @return void
     */
    public function testDestroy()
    {
        $team = $this->user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->make());
        $language = $project->languages()->save(factory(Language::class)->make());

        $this->assertCount(1, $project->users);
        $this->assertCount(1, $project->languages);

        $this->json('DELETE', 'api/projects/1')
            ->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDeleted($project);

        $this->assertDatabaseMissing('model_has_users', [
            'user_id' => $this->user->id,
            'model_type' => 'project',
            'model_id' => $project->id,
        ]);

        $this->assertDatabaseMissing('model_has_languages', [
            'language_id' => $language->id,
            'model_type' => 'project',
            'model_id' => $project->id,
        ]);
    }
}
