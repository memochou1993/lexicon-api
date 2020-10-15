<?php

namespace Tests\Feature\Api;

use App\Enums\ErrorType;
use App\Enums\PermissionType;
use App\Models\Key;
use App\Models\Project;
use App\Models\Team;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
        $team = Team::factory()->create();

        /** @var Project $project */
        $project = $team->projects()->save(Project::factory()->make());
        $project->keys()->save(Key::factory()->make());

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
        $team = Team::factory()->create();

        /** @var Project $project */
        $project = $team->projects()->save(Project::factory()->make());

        $data = Key::factory()->make()->toArray();

        $this->json('POST', 'api/projects/'.$project->id.'/keys', $data)
            ->assertCreated();

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
        $team = Team::factory()->create();

        /** @var Project $project */
        $project = $team->projects()->save(Project::factory()->make());
        $project->keys()->save(Key::factory()->make([
            'name' => 'Unique Key',
        ]));

        $data = Key::factory()->make([
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
        Sanctum::actingAs($this->user, [
            PermissionType::KEY_VIEW,
        ]);

        /** @var Team $team */
        $team = Team::factory()->create();

        /** @var Project $project */
        $project = $team->projects()->save(Project::factory()->make());

        /** @var Key $key */
        $key = $project->keys()->save(Key::factory()->make());

        $this->json('GET', 'api/keys/'.$key->id, [
            'relations' => 'values',
        ])
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
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
        Sanctum::actingAs($this->user, [
            PermissionType::KEY_UPDATE,
        ]);

        /** @var Team $team */
        $team = Team::factory()->create();

        /** @var Project $project */
        $project = $team->projects()->save(Project::factory()->make());

        /** @var Key $key */
        $key = $project->keys()->save(Key::factory()->make());

        $data = Key::factory()->make([
            'name' => 'New Key',
        ])->toArray();

        $this->json('PATCH', 'api/keys/'.$key->id, $data)
            ->assertOk();
    }

    /**
     * @return void
     */
    public function testUpdateDuplicate()
    {
        Sanctum::actingAs($this->user, [
            PermissionType::KEY_UPDATE,
        ]);

        /** @var Team $team */
        $team = Team::factory()->create();

        /** @var Project $project */
        $project = $team->projects()->save(Project::factory()->make());

        /** @var Collection $keys */
        $keys = $project->keys()->saveMany(Key::factory()->count(2)->make());

        $data = Key::factory()->make([
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
        Sanctum::actingAs($this->user, [
            PermissionType::KEY_DELETE,
        ]);

        /** @var Team $team */
        $team = Team::factory()->create();

        /** @var Project $project */
        $project = $team->projects()->save(Project::factory()->make());

        /** @var Key $key */
        $key = $project->keys()->save(Key::factory()->make());

        $this->json('DELETE', 'api/keys/'.$key->id)
            ->assertNoContent();

        $this->assertDeleted($key);
    }

    /**
     * @return void
     */
    public function testViewAllByGuest()
    {
        Sanctum::actingAs($this->user, [
            PermissionType::PROJECT_VIEW,
            PermissionType::KEY_VIEW_ANY,
        ]);

        $this->flushEventListeners(Project::class);

        /** @var Team $team */
        $team = Team::factory()->create();

        /** @var Project $project */
        $project = $team->projects()->save(Project::factory()->make());
        $project->keys()->save(Key::factory()->make());

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
    public function testCreateByGuest()
    {
        Sanctum::actingAs($this->user, [
            PermissionType::PROJECT_VIEW,
            PermissionType::KEY_CREATE,
        ]);

        $this->flushEventListeners(Project::class);

        /** @var Team $team */
        $team = Team::factory()->create();

        /** @var Project $project */
        $project = $team->projects()->save(Project::factory()->make());

        $data = Key::factory()->make()->toArray();

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
    public function testViewByGuest()
    {
        Sanctum::actingAs($this->user, [
            PermissionType::KEY_VIEW,
        ]);

        $this->flushEventListeners(Project::class);

        /** @var Team $team */
        $team = Team::factory()->create();

        /** @var Project $project */
        $project = $team->projects()->save(Project::factory()->make());

        /** @var Key $key */
        $key = $project->keys()->save(Key::factory()->make());

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
    public function testUpdateByGuest()
    {
        Sanctum::actingAs($this->user, [
            PermissionType::KEY_UPDATE,
        ]);

        $this->flushEventListeners(Project::class);

        /** @var Team $team */
        $team = Team::factory()->create();

        /** @var Project $project */
        $project = $team->projects()->save(Project::factory()->make());

        /** @var Key $key */
        $key = $project->keys()->save(Key::factory()->make());

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
    public function testDeleteByGuest()
    {
        Sanctum::actingAs($this->user, [
            PermissionType::KEY_DELETE,
        ]);

        $this->flushEventListeners(Project::class);

        /** @var Team $team */
        $team = Team::factory()->create();

        /** @var Project $project */
        $project = $team->projects()->save(Project::factory()->make());

        /** @var Key $key */
        $key = $project->keys()->save(Key::factory()->make());

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
        $team = Team::factory()->create();

        /** @var Project $project */
        $project = $team->projects()->save(Project::factory()->make());
        $project->keys()->save(Key::factory()->make());

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
        $team = Team::factory()->create();

        /** @var Project $project */
        $project = $team->projects()->save(Project::factory()->make());

        $data = Key::factory()->make()->toArray();

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
        $team = Team::factory()->create();

        /** @var Project $project */
        $project = $team->projects()->save(Project::factory()->make());

        /** @var Key $key */
        $key = $project->keys()->save(Key::factory()->make());

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
        $team = Team::factory()->create();

        /** @var Project $project */
        $project = $team->projects()->save(Project::factory()->make());

        /** @var Key $key */
        $key = $project->keys()->save(Key::factory()->make());

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
        $team = Team::factory()->create();

        /** @var Project $project */
        $project = $team->projects()->save(Project::factory()->make());

        /** @var Key $key */
        $key = $project->keys()->save(Key::factory()->make());

        $response = $this->json('DELETE', 'api/keys/'.$key->id)
            ->assertForbidden();

        $this->assertEquals(
            ErrorType::PERMISSION_DENIED,
            $response->exception->getCode()
        );
    }
}
