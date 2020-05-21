<?php

namespace Tests\Feature;

use App\Models\Language;
use App\Models\Project;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProjectControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function testShow()
    {
        $user = Sanctum::actingAs($this->user, ['view-project']);

        $team = $user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->make());

        $this->json('GET', 'api/projects/'.$project->id, [
            'relations' => 'users,team,languages',
        ])
            ->assertOk()
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
    public function testUpdate()
    {
        $user = Sanctum::actingAs($this->user, ['update-project']);

        $team = $user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->make());

        $data = factory(Project::class)->make([
            'name' => 'New Project',
        ])->toArray();

        $this->json('PATCH', 'api/projects/'.$project->id, $data)
            ->assertOk()
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
        $user = Sanctum::actingAs($this->user, ['update-project']);

        $team = $user->teams()->save(factory(Team::class)->make());
        $projects = $team->projects()->saveMany(factory(Project::class, 2)->make());

        $data = factory(Project::class)->make([
            'name' => $projects->last()->name,
        ])->toArray();

        $this->json('PATCH', 'api/projects/'.$projects->first()->id, $data)
            ->assertJsonValidationErrors([
                'name',
            ]);
    }

    /**
     * @return void
     */
    public function testDestroy()
    {
        $user = Sanctum::actingAs($this->user, ['delete-project']);

        $team = $user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->make());
        $language = $project->languages()->save(factory(Language::class)->make());

        $this->assertCount(1, $project->users);
        $this->assertCount(1, $project->languages);

        $this->json('DELETE', 'api/projects/'.$project->id)
            ->assertNoContent();

        $this->assertDeleted($project);

        $this->assertDatabaseMissing('model_has_users', [
            'user_id' => $user->id,
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
    public function testGuestView()
    {
        $user = Sanctum::actingAs($this->user, ['view-project']);

        $team = $user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->withoutEvents()->make());

        $this->json('GET', 'api/projects/'.$project->id)
            ->assertForbidden();
    }

    /**
     * @return void
     */
    public function testGuestUpdate()
    {
        $user = Sanctum::actingAs($this->user, ['update-project']);

        $team = $user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->withoutEvents()->make());

        $this->json('PATCH', 'api/projects/'.$project->id)
            ->assertForbidden();
    }

    /**
     * @return void
     */
    public function testGuestDelete()
    {
        $user = Sanctum::actingAs($this->user, ['delete-project']);

        $team = $user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->withoutEvents()->make());

        $this->json('DELETE', 'api/projects/'.$project->id)
            ->assertForbidden();
    }

    /**
     * @return void
     */
    public function testViewWithoutPermission()
    {
        $user = Sanctum::actingAs($this->user);

        $team = $user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->make());

        $this->json('GET', 'api/projects/'.$project->id)
            ->assertForbidden();
    }

    /**
     * @return void
     */
    public function testUpdateWithoutPermission()
    {
        $user = Sanctum::actingAs($this->user);

        $team = $user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->make());

        $this->json('PATCH', 'api/projects/'.$project->id)
            ->assertForbidden();
    }

    /**
     * @return void
     */
    public function testDeleteWithoutPermission()
    {
        $user = Sanctum::actingAs($this->user);

        $team = $user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->make());

        $this->json('DELETE', 'api/projects/'.$project->id)
            ->assertForbidden();
    }
}
