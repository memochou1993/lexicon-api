<?php

namespace Tests\Feature\Api;

use App\Enums\ErrorType;
use App\Enums\PermissionType;
use App\Models\Key;
use App\Models\Project;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class KeyControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function testIndex()
    {
        $user = Sanctum::actingAs($this->user, [
            PermissionType::PROJECT_VIEW,
            PermissionType::KEY_VIEW_ANY,
        ]);

        $team = $user->teams()->save(factory(Team::class)->make());
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
        $user = Sanctum::actingAs($this->user, [
            PermissionType::PROJECT_VIEW,
            PermissionType::KEY_CREATE,
        ]);

        $team = $user->teams()->save(factory(Team::class)->make());
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
        $user = Sanctum::actingAs($this->user, [
            PermissionType::PROJECT_VIEW,
            PermissionType::KEY_CREATE,
        ]);

        $team = $user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->make());
        $project->keys()->save(factory(Key::class)->make([
            'name' => 'Unique Key',
        ]));

        $data = factory(Key::class)->make([
            'name' => 'Unique Key',
        ])->toArray();

        $this->json('POST', 'api/projects/'.$project->id.'/keys', $data)
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
        $user = Sanctum::actingAs($this->user, [PermissionType::KEY_VIEW]);

        $team = $user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->make());
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
        $user = Sanctum::actingAs($this->user, [PermissionType::KEY_UPDATE]);

        $team = $user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->make());
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
        $user = Sanctum::actingAs($this->user, [PermissionType::KEY_UPDATE]);

        $team = $user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->make());
        $keys = $project->keys()->saveMany(factory(Key::class, 2)->make());

        $data = factory(Key::class)->make([
            'name' => $keys->last()->name,
        ])->toArray();

        $this->json('PATCH', 'api/keys/'.$keys->first()->id, $data)
            ->assertJsonValidationErrors([
                'name',
            ]);
    }

    /**
     * @return void
     */
    public function testDestroy()
    {
        $user = Sanctum::actingAs($this->user, [PermissionType::KEY_DELETE]);

        $team = $user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->make());
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
        $user = Sanctum::actingAs($this->user, [
            PermissionType::PROJECT_VIEW,
            PermissionType::KEY_VIEW_ANY,
        ]);

        $team = $user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->disableEvents()->make());
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
        $user = Sanctum::actingAs($this->user, [
            PermissionType::PROJECT_VIEW,
            PermissionType::KEY_CREATE,
        ]);

        $team = $user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->disableEvents()->make());

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
        $user = Sanctum::actingAs($this->user, [PermissionType::KEY_VIEW]);

        $team = $user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->disableEvents()->make());
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
        $user = Sanctum::actingAs($this->user, [PermissionType::KEY_UPDATE]);

        $team = $user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->disableEvents()->make());
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
        $user = Sanctum::actingAs($this->user, [PermissionType::KEY_DELETE]);

        $team = $user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->disableEvents()->make());
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
        $user = Sanctum::actingAs($this->user);

        $team = $user->teams()->save(factory(Team::class)->make());
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
        $user = Sanctum::actingAs($this->user);

        $team = $user->teams()->save(factory(Team::class)->make());
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
        $user = Sanctum::actingAs($this->user);

        $team = $user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->make());
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
        $user = Sanctum::actingAs($this->user);

        $team = $user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->make());
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
        $user = Sanctum::actingAs($this->user);

        $team = $user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->make());
        $key = $project->keys()->save(factory(Key::class)->make());

        $response = $this->json('DELETE', 'api/keys/'.$key->id)
            ->assertForbidden();

        $this->assertEquals(
            ErrorType::PERMISSION_DENIED,
            $response->exception->getCode()
        );
    }
}
