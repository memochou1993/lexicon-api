<?php

namespace Tests\Feature\Api;

use App\Enums\ErrorType;
use App\Enums\PermissionType;
use App\Models\Hook;
use App\Models\Project;
use App\Models\Team;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class HookControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function testStore()
    {
        Sanctum::actingAs($this->user, [
            PermissionType::PROJECT_VIEW,
            PermissionType::HOOK_CREATE,
        ]);

        /** @var Team $team */
        $team = Team::factory()->create();

        /** @var Project $project */
        $project = $team->projects()->save(Project::factory()->make());

        $data = Hook::factory()->make()->toArray();

        $this->json('POST', 'api/projects/'.$project->id.'/hooks', $data)
            ->assertCreated();

        $this->assertCount(1, $project->refresh()->hooks);
    }

    /**
     * @return void
     */
    public function testStoreDuplicate()
    {
        Sanctum::actingAs($this->user, [
            PermissionType::PROJECT_VIEW,
            PermissionType::HOOK_CREATE,
        ]);

        /** @var Team $team */
        $team = Team::factory()->create();

        /** @var Project $project */
        $project = $team->projects()->save(Project::factory()->make());
        $project->hooks()->save(Hook::factory()->make([
            'url' => 'http://unique.test',
        ]));

        $data = Hook::factory()->make([
            'url' => 'http://unique.test',
        ])->toArray();

        $this->json('POST', 'api/projects/'.$project->id.'/hooks', $data)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors([
                'url',
            ]);

        $this->assertCount(1, $project->refresh()->hooks);
    }

    /**
     * @return void
     */
    public function testShow()
    {
        Sanctum::actingAs($this->user, [
            PermissionType::PROJECT_VIEW,
            PermissionType::HOOK_VIEW,
        ]);

        /** @var Team $team */
        $team = Team::factory()->create();

        /** @var Project $project */
        $project = $team->projects()->save(Project::factory()->make());

        /** @var Hook $hook */
        $hook = $project->hooks()->save(Hook::factory()->make());

        $this->json('GET', 'api/hooks/'.$hook->id, [
            'relations' => '',
        ])
            ->assertOk()
            ->assertJson([
                'data' => $hook->toArray(),
            ]);
    }

    /**
     * @return void
     */
    public function testUpdate()
    {
        Sanctum::actingAs($this->user, [
            PermissionType::PROJECT_VIEW,
            PermissionType::HOOK_UPDATE,
        ]);

        /** @var Team $team */
        $team = Team::factory()->create();

        /** @var Project $project */
        $project = $team->projects()->save(Project::factory()->make());

        /** @var Hook $hook */
        $hook = $project->hooks()->save(Hook::factory()->make());

        $data = Hook::factory()->make([
            'url' => 'http://new.test',
        ])->toArray();

        $this->json('PATCH', 'api/hooks/'.$hook->id, $data)
            ->assertOk();
    }

    /**
     * @return void
     */
    public function testUpdateDuplicate()
    {
        Sanctum::actingAs($this->user, [
            PermissionType::PROJECT_VIEW,
            PermissionType::HOOK_UPDATE,
        ]);

        /** @var Team $team */
        $team = Team::factory()->create();

        /** @var Project $project */
        $project = $team->projects()->save(Project::factory()->make());

        /** @var Collection $hooks */
        $hooks = $project->hooks()->saveMany(Hook::factory()->count(2)->make());

        $data = Hook::factory()->make([
            'url' => $hooks->last()->url,
        ])->toArray();

        $this->json('PATCH', 'api/hooks/'.$hooks->first()->id, $data)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors([
                'url',
            ]);
    }

    /**
     * @return void
     */
    public function testDestroy()
    {
        Sanctum::actingAs($this->user, [
            PermissionType::PROJECT_VIEW,
            PermissionType::HOOK_DELETE,
        ]);

        /** @var Team $team */
        $team = Team::factory()->create();

        /** @var Project $project */
        $project = $team->projects()->save(Project::factory()->make());

        /** @var Hook $hook */
        $hook = $project->hooks()->save(Hook::factory()->make());

        $this->json('DELETE', 'api/hooks/'.$hook->id)
            ->assertNoContent();

        $this->assertDeleted($hook);
    }

    /**
     * @return void
     */
    public function testCreateByGuest()
    {
        Sanctum::actingAs($this->user, [
            PermissionType::PROJECT_VIEW,
            PermissionType::HOOK_CREATE,
        ]);

        $this->flushEventListeners(Project::class);

        /** @var Team $team */
        $team = Team::factory()->create();

        /** @var Project $project */
        $project = $team->projects()->save(Project::factory()->make());

        $data = Hook::factory()->make()->toArray();

        $response = $this->json('POST', 'api/projects/'.$project->id.'/hooks', $data)
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
            PermissionType::HOOK_VIEW,
        ]);

        $this->flushEventListeners(Project::class);

        /** @var Team $team */
        $team = Team::factory()->create();

        /** @var Project $project */
        $project = $team->projects()->save(Project::factory()->make());

        /** @var Hook $hook */
        $hook = $project->hooks()->save(Hook::factory()->make());

        $response = $this->json('GET', 'api/hooks/'.$hook->id)
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
            PermissionType::HOOK_UPDATE,
        ]);

        $this->flushEventListeners(Project::class);

        /** @var Team $team */
        $team = Team::factory()->create();

        /** @var Project $project */
        $project = $team->projects()->save(Project::factory()->make());

        /** @var Hook $hook */
        $hook = $project->hooks()->save(Hook::factory()->make());

        $response = $this->json('PATCH', 'api/hooks/'.$hook->id)
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
            PermissionType::HOOK_DELETE,
        ]);

        $this->flushEventListeners(Project::class);

        /** @var Team $team */
        $team = Team::factory()->create();

        /** @var Project $project */
        $project = $team->projects()->save(Project::factory()->make());

        /** @var Hook $hook */
        $hook = $project->hooks()->save(Hook::factory()->make());

        $response = $this->json('DELETE', 'api/hooks/'.$hook->id)
            ->assertForbidden();

        $this->assertEquals(
            ErrorType::USER_NOT_IN_PROJECT,
            $response->exception->getCode()
        );
    }
}
