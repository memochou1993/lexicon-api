<?php

namespace Tests\Feature\Api;

use App\Enums\ErrorType;
use App\Enums\PermissionType;
use App\Models\Key;
use App\Models\Project;
use App\Models\Team;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class KeyControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function testIndex()
    {
        Sanctum::actingAs($this->user, [
            PermissionType::PROJECT_VIEW,
            PermissionType::KEY_VIEW_ANY,
        ]);

        /** @var Team $team */
        $team = factory(Team::class)->create();

        /** @var Project $project */
        $project = $team->projects()->save(factory(Project::class)->make());
        $project->keys()->save(factory(Key::class)->make());

        $this->json('GET', 'api/projects/'.$project->id.'/keys', [
            'relations' => 'values',
        ])
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    [
                        'values',
                    ],
                ],
            ])
            ->assertJson([
                'data' => $project->keys->toArray(),
            ]);
    }

    /**
     * @return void
     */
    public function testStore()
    {
        Sanctum::actingAs($this->user, [
            PermissionType::PROJECT_VIEW,
            PermissionType::KEY_CREATE,
        ]);

        /** @var Team $team */
        $team = factory(Team::class)->create();

        /** @var Project $project */
        $project = $team->projects()->save(factory(Project::class)->make());

        $data = factory(Key::class)->make()->toArray();

        $this->json('POST', 'api/projects/'.$project->id.'/keys', $data)
            ->assertCreated()
            ->assertJson([
                'data' => $data,
            ]);

        $this->assertDatabaseHas('keys', $data);

        $this->assertCount(1, $project->refresh()->keys);
    }

    /**
     * @return void
     */
    public function testStoreDuplicate()
    {
        Sanctum::actingAs($this->user, [
            PermissionType::PROJECT_VIEW,
            PermissionType::KEY_CREATE,
        ]);

        /** @var Team $team */
        $team = factory(Team::class)->create();

        /** @var Project $project */
        $project = $team->projects()->save(factory(Project::class)->make());
        $project->keys()->save(factory(Key::class)->make([
            'name' => 'Unique Key',
        ]));

        $data = factory(Key::class)->make([
            'name' => 'Unique Key',
        ])->toArray();

        $this->json('POST', 'api/projects/'.$project->id.'/keys', $data)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors([
                'name',
            ]);

        $this->assertCount(1, $project->refresh()->keys);
    }

    /**
     * @return void
     */
    public function testShow()
    {
        Sanctum::actingAs($this->user, [PermissionType::KEY_VIEW]);

        /** @var Team $team */
        $team = factory(Team::class)->create();

        /** @var Project $project */
        $project = $team->projects()->save(factory(Project::class)->make());

        /** @var Key $key */
        $key = $project->keys()->save(factory(Key::class)->make());

        $this->json('GET', 'api/keys/'.$key->id, [
            'relations' => 'project,values',
        ])
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'project',
                    'values',
                ],
            ])
            ->assertJson([
                'data' => $key->toArray(),
            ]);
    }

    /**
     * @return void
     */
    public function testUpdate()
    {
        Sanctum::actingAs($this->user, [PermissionType::KEY_UPDATE]);

        /** @var Team $team */
        $team = factory(Team::class)->create();

        /** @var Project $project */
        $project = $team->projects()->save(factory(Project::class)->make());

        /** @var Key $key */
        $key = $project->keys()->save(factory(Key::class)->make());

        $data = factory(Key::class)->make([
            'name' => 'New Key',
        ])->toArray();

        $this->json('PATCH', 'api/keys/'.$key->id, $data)
            ->assertOk()
            ->assertJson([
                'data' => $data,
            ]);

        $this->assertDatabaseHas('keys', $data);
    }

    /**
     * @return void
     */
    public function testUpdateDuplicate()
    {
        Sanctum::actingAs($this->user, [PermissionType::KEY_UPDATE]);

        /** @var Team $team */
        $team = factory(Team::class)->create();

        /** @var Project $project */
        $project = $team->projects()->save(factory(Project::class)->make());

        /** @var Collection $keys */
        $keys = $project->keys()->saveMany(factory(Key::class, 2)->make());

        $data = factory(Key::class)->make([
            'name' => $keys->last()->name,
        ])->toArray();

        $this->json('PATCH', 'api/keys/'.$keys->first()->id, $data)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors([
                'name',
            ]);
    }

    /**
     * @return void
     */
    public function testDestroy()
    {
        Sanctum::actingAs($this->user, [PermissionType::KEY_DELETE]);

        /** @var Team $team */
        $team = factory(Team::class)->create();

        /** @var Project $project */
        $project = $team->projects()->save(factory(Project::class)->make());

        /** @var Key $key */
        $key = $project->keys()->save(factory(Key::class)->make());

        $this->json('DELETE', 'api/keys/'.$key->id)
            ->assertNoContent();

        $this->assertDeleted($key);
    }

    /**
     * @return void
     */
    public function testGuestViewAll()
    {
        Sanctum::actingAs($this->user, [
            PermissionType::PROJECT_VIEW,
            PermissionType::KEY_VIEW_ANY,
        ]);

        $this->flushEventListeners(Project::class);

        /** @var Team $team */
        $team = factory(Team::class)->create();

        /** @var Project $project */
        $project = $team->projects()->save(factory(Project::class)->make());
        $project->keys()->save(factory(Key::class)->make());

        $response = $this->json('GET', 'api/projects/'.$project->id.'/keys')
            ->assertForbidden();

        $this->assertEquals(
            ErrorType::USER_NOT_IN_PROJECT,
            $response->exception->getCode()
        );
    }

    /**
     * @return void
     */
    public function testGuestCreate()
    {
        Sanctum::actingAs($this->user, [
            PermissionType::PROJECT_VIEW,
            PermissionType::KEY_CREATE,
        ]);

        $this->flushEventListeners(Project::class);

        /** @var Team $team */
        $team = factory(Team::class)->create();

        /** @var Project $project */
        $project = $team->projects()->save(factory(Project::class)->make());

        $data = factory(Key::class)->make()->toArray();

        $response = $this->json('POST', 'api/projects/'.$project->id.'/keys', $data)
            ->assertForbidden();

        $this->assertEquals(
            ErrorType::USER_NOT_IN_PROJECT,
            $response->exception->getCode()
        );
    }

    /**
     * @return void
     */
    public function testGuestView()
    {
        Sanctum::actingAs($this->user, [PermissionType::KEY_VIEW]);

        $this->flushEventListeners(Project::class);

        /** @var Team $team */
        $team = factory(Team::class)->create();

        /** @var Project $project */
        $project = $team->projects()->save(factory(Project::class)->make());

        /** @var Key $key */
        $key = $project->keys()->save(factory(Key::class)->make());

        $response = $this->json('GET', 'api/keys/'.$key->id)
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
        Sanctum::actingAs($this->user, [PermissionType::KEY_UPDATE]);

        $this->flushEventListeners(Project::class);

        /** @var Team $team */
        $team = factory(Team::class)->create();

        /** @var Project $project */
        $project = $team->projects()->save(factory(Project::class)->make());

        /** @var Key $key */
        $key = $project->keys()->save(factory(Key::class)->make());

        $response = $this->json('PATCH', 'api/keys/'.$key->id)
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
        Sanctum::actingAs($this->user, [PermissionType::KEY_DELETE]);

        $this->flushEventListeners(Project::class);

        /** @var Team $team */
        $team = factory(Team::class)->create();

        /** @var Project $project */
        $project = $team->projects()->save(factory(Project::class)->make());

        /** @var Key $key */
        $key = $project->keys()->save(factory(Key::class)->make());

        $response = $this->json('DELETE', 'api/keys/'.$key->id)
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
        Sanctum::actingAs($this->user);

        /** @var Team $team */
        $team = factory(Team::class)->create();

        /** @var Project $project */
        $project = $team->projects()->save(factory(Project::class)->make());
        $project->keys()->save(factory(Key::class)->make());

        $response = $this->json('GET', 'api/projects/'.$project->id.'/keys')
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
        Sanctum::actingAs($this->user);

        /** @var Team $team */
        $team = factory(Team::class)->create();

        /** @var Project $project */
        $project = $team->projects()->save(factory(Project::class)->make());

        $data = factory(Key::class)->make()->toArray();

        $response = $this->json('POST', 'api/projects/'.$project->id.'/keys', $data)
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
        Sanctum::actingAs($this->user);

        /** @var Team $team */
        $team = factory(Team::class)->create();

        /** @var Project $project */
        $project = $team->projects()->save(factory(Project::class)->make());

        /** @var Key $key */
        $key = $project->keys()->save(factory(Key::class)->make());

        $response = $this->json('GET', 'api/keys/'.$key->id)
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
        Sanctum::actingAs($this->user);

        /** @var Team $team */
        $team = factory(Team::class)->create();

        /** @var Project $project */
        $project = $team->projects()->save(factory(Project::class)->make());

        /** @var Key $key */
        $key = $project->keys()->save(factory(Key::class)->make());

        $response = $this->json('PATCH', 'api/keys/'.$key->id)
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
        Sanctum::actingAs($this->user);

        /** @var Team $team */
        $team = factory(Team::class)->create();

        /** @var Project $project */
        $project = $team->projects()->save(factory(Project::class)->make());

        /** @var Key $key */
        $key = $project->keys()->save(factory(Key::class)->make());

        $response = $this->json('DELETE', 'api/keys/'.$key->id)
            ->assertForbidden();

        $this->assertEquals(
            ErrorType::PERMISSION_DENIED,
            $response->exception->getCode()
        );
    }
}
