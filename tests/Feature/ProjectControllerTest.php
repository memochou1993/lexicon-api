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
    public function testShow()
    {
        $team = $this->user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->make());

        $this->json('GET', 'api/projects/'.$project->id, [
            'relations' => 'users,team,languages',
        ])
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data' => [
                    'users',
                    'team',
                    'languages',
                ],
            ])
            ->assertJson([
                'data' => $project->toArray(),
            ]);
    }

    /**
     * @return void
     */
    public function testViewForbidden()
    {
        $user = factory(User::class)->create();
        $team = $user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->withoutEvents()->make());

        $this->json('GET', 'api/projects/'.$project->id)
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * @return void
     */
    public function testUpdate()
    {
        $team = $this->user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->make());

        $data = factory(Project::class)->make([
            'name' => 'New Project',
        ])->toArray();

        $this->json('PATCH', 'api/projects/'.$project->id, $data)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'data' => $data,
            ]);

        $this->assertDatabaseHas('projects', $data);
    }

    /**
     * @return void
     */
    public function testUpdateDuplicate()
    {
        $team = $this->user->teams()->save(factory(Team::class)->make());
        $projects = $team->projects()->saveMany(factory(Project::class, 2)->make());

        $data = factory(Project::class)->make([
            'name' => $projects->last()->name,
        ])->toArray();

        $this->json('PATCH', 'api/projects/'.$projects->first()->id, $data)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure([
                'errors' => [
                    'name',
                ],
            ]);
    }

    /**
     * @return void
     */
    public function testUpdateForbidden()
    {
        $user = factory(User::class)->create();
        $team = $user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->withoutEvents()->make());

        $this->json('PATCH', 'api/projects/'.$project->id)
            ->assertStatus(Response::HTTP_FORBIDDEN);
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

        $this->json('DELETE', 'api/projects/'.$project->id)
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

    /**
     * @return void
     */
    public function testDeleteForbidden()
    {
        $user = factory(User::class)->create();
        $team = $user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->withoutEvents()->make());

        $this->json('DELETE', 'api/projects/'.$project->id)
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }
}
