<?php

namespace Tests\Feature\Api;

use App\Enums\ErrorType;
use App\Enums\PermissionType;
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
    public function testIndex()
    {
        $user = Sanctum::actingAs($this->user, [
            PermissionType::TEAM_VIEW,
            PermissionType::PROJECT_VIEW_ANY,
        ]);

        $team = $user->teams()->save(factory(Team::class)->make());
        $team->projects()->save(factory(Project::class)->make());

        $this->json('GET', 'api/teams/'.$team->id.'/projects', [
            'relations' => 'users,languages',
        ])
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    [
                        'users',
                        'languages',
                    ],
                ],
            ]);
    }

    /**
     * @return void
     */
    public function testStore()
    {
        $user = Sanctum::actingAs($this->user, [
            PermissionType::TEAM_VIEW,
            PermissionType::PROJECT_CREATE,
        ]);

        $team = $user->teams()->save(factory(Team::class)->make());

        $data = factory(Project::class)->make()->toArray();

        $this->json('POST', 'api/teams/'.$team->id.'/projects', $data)
            ->assertCreated()
            ->assertJson([
                'data' => [
                    'name' => $data['name'],
                ],
            ]);

        $this->assertCount(1, $team->refresh()->projects);
    }

    /**
     * @return void
     */
    public function testStoreDuplicate()
    {
        $user = Sanctum::actingAs($this->user, [
            PermissionType::TEAM_VIEW,
            PermissionType::PROJECT_CREATE,
        ]);

        $team = $user->teams()->save(factory(Team::class)->make());
        $team->projects()->save(factory(Project::class)->make([
            'name' => 'Unique Project',
        ]));

        $data = factory(Project::class)->make([
            'name' => 'Unique Project',
        ])->toArray();

        $this->json('POST', 'api/teams/'.$team->id.'/projects', $data)
            ->assertJsonValidationErrors([
                'name',
            ]);

        $this->assertCount(1, $team->refresh()->projects);
    }

    /**
     * @return void
     */
    public function testShow()
    {
        $user = Sanctum::actingAs($this->user, [PermissionType::PROJECT_VIEW]);

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
                'data' => [
                    'name' => $project->name,
                ],
            ]);
    }

    /**
     * @return void
     */
    public function testUpdate()
    {
        $user = Sanctum::actingAs($this->user, [PermissionType::PROJECT_UPDATE]);

        $team = $user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->make());

        $data = factory(Project::class)->make([
            'name' => 'New Project',
        ])->toArray();

        $this->json('PATCH', 'api/projects/'.$project->id, $data)
            ->assertOk()
            ->assertJson([
                'data' => [
                    'name' => $data['name'],
                ],
            ]);

        $this->assertDatabaseHas('projects', $data);
    }

    /**
     * @return void
     */
    public function testUpdateDuplicate()
    {
        $user = Sanctum::actingAs($this->user, [PermissionType::PROJECT_UPDATE]);

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
        $user = Sanctum::actingAs($this->user, [PermissionType::PROJECT_DELETE]);

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
    public function testGuestViewAll()
    {
        Sanctum::actingAs($this->user, [
            PermissionType::TEAM_VIEW,
            PermissionType::PROJECT_VIEW_ANY,
        ]);

        $team = factory(Team::class)->create();
        $team->projects()->save(factory(Project::class)->make());

        $response = $this->json('GET', 'api/teams/'.$team->id.'/projects')
            ->assertForbidden();

        $this->assertEquals(
            ErrorType::USER_NOT_IN_TEAM,
            $response->exception->getCode()
        );
    }

    /**
     * @return void
     */
    public function testGuestCreate()
    {
        Sanctum::actingAs($this->user, [
            PermissionType::TEAM_VIEW,
            PermissionType::PROJECT_CREATE,
        ]);

        $team = factory(Team::class)->create();

        $data = factory(Project::class)->make()->toArray();

        $response = $this->json('POST', 'api/teams/'.$team->id.'/projects', $data)
            ->assertForbidden();

        $this->assertEquals(
            ErrorType::USER_NOT_IN_TEAM,
            $response->exception->getCode()
        );
    }

    /**
     * @return void
     */
    public function testGuestView()
    {
        $user = Sanctum::actingAs($this->user, [PermissionType::PROJECT_VIEW]);

        $team = $user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->disableEvents()->make());

        $response = $this->json('GET', 'api/projects/'.$project->id)
            ->assertForbidden();

        $this->assertEquals(
            ErrorType::USER_NOT_IN_PROJECT,
            $response->exception->getCode()
        );
    }

    /**
     * @return void
     */
    public function testGuestUpdate()
    {
        $user = Sanctum::actingAs($this->user, [PermissionType::PROJECT_UPDATE]);

        $team = $user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->disableEvents()->make());

        $response = $this->json('PATCH', 'api/projects/'.$project->id)
            ->assertForbidden();

        $this->assertEquals(
            ErrorType::USER_NOT_IN_PROJECT,
            $response->exception->getCode()
        );
    }

    /**
     * @return void
     */
    public function testGuestDelete()
    {
        $user = Sanctum::actingAs($this->user, [PermissionType::PROJECT_DELETE]);

        $team = $user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->disableEvents()->make());

        $response = $this->json('DELETE', 'api/projects/'.$project->id)
            ->assertForbidden();

        $this->assertEquals(
            ErrorType::USER_NOT_IN_PROJECT,
            $response->exception->getCode()
        );
    }

    /**
     * @return void
     */
    public function testViewAllWithoutPermission()
    {
        $user = Sanctum::actingAs($this->user);

        $team = $user->teams()->save(factory(Team::class)->make());
        $team->projects()->save(factory(Project::class)->make());

        $response = $this->json('GET', 'api/teams/'.$team->id.'/projects')
            ->assertForbidden();

        $this->assertEquals(
            ErrorType::PERMISSION_DENIED,
            $response->exception->getCode()
        );
    }

    /**
     * @return void
     */
    public function testCreateWithoutPermission()
    {
        $user = Sanctum::actingAs($this->user);

        $team = $user->teams()->save(factory(Team::class)->make());

        $data = factory(Project::class)->make()->toArray();

        $response = $this->json('POST', 'api/teams/'.$team->id.'/projects', $data)
            ->assertForbidden();

        $this->assertEquals(
            ErrorType::PERMISSION_DENIED,
            $response->exception->getCode()
        );
    }

    /**
     * @return void
     */
    public function testViewWithoutPermission()
    {
        $user = Sanctum::actingAs($this->user);

        $team = $user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->make());

        $response = $this->json('GET', 'api/projects/'.$project->id)
            ->assertForbidden();

        $this->assertEquals(
            ErrorType::PERMISSION_DENIED,
            $response->exception->getCode()
        );
    }

    /**
     * @return void
     */
    public function testUpdateWithoutPermission()
    {
        $user = Sanctum::actingAs($this->user);

        $team = $user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->make());

        $response = $this->json('PATCH', 'api/projects/'.$project->id)
            ->assertForbidden();

        $this->assertEquals(
            ErrorType::PERMISSION_DENIED,
            $response->exception->getCode()
        );
    }

    /**
     * @return void
     */
    public function testDeleteWithoutPermission()
    {
        $user = Sanctum::actingAs($this->user);

        $team = $user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->make());

        $response = $this->json('DELETE', 'api/projects/'.$project->id)
            ->assertForbidden();

        $this->assertEquals(
            ErrorType::PERMISSION_DENIED,
            $response->exception->getCode()
        );
    }
}
