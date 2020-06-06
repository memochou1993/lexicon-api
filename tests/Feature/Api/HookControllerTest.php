<?php

namespace Tests\Feature\Api;

use App\Enums\PermissionType;
use App\Models\Hook;
use App\Models\Project;
use App\Models\Team;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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
            // TODO
        ]);

        /** @var Team $team */
        $team = factory(Team::class)->create();

        /** @var Project $project */
        $project = $team->projects()->save(factory(Project::class)->make());

        $data = factory(Hook::class)->make()->toArray();

        $this->json('POST', 'api/projects/'.$project->id.'/hooks', $data)
            ->assertCreated()
            ->assertJson([
                'data' => $data,
            ]);

        $this->assertDatabaseHas('hooks', $data);

        $this->assertCount(1, $project->refresh()->hooks);
    }

    /**
     * @return void
     */
    public function testStoreDuplicate()
    {
        Sanctum::actingAs($this->user, [
            // TODO
        ]);

        /** @var Team $team */
        $team = factory(Team::class)->create();

        /** @var Project $project */
        $project = $team->projects()->save(factory(Project::class)->make());
        $project->hooks()->save(factory(Hook::class)->make([
            'url' => 'http://unique.test',
        ]));

        $data = factory(Hook::class)->make([
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
            // TODO
        ]);

        /** @var Team $team */
        $team = factory(Team::class)->create();

        /** @var Project $project */
        $project = $team->projects()->save(factory(Project::class)->make());

        /** @var Hook $hook */
        $hook = $project->hooks()->save(factory(Hook::class)->make());

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
            // TODO
        ]);

        /** @var Team $team */
        $team = factory(Team::class)->create();

        /** @var Project $project */
        $project = $team->projects()->save(factory(Project::class)->make());

        /** @var Hook $hook */
        $hook = $project->hooks()->save(factory(Hook::class)->make());

        $data = factory(Hook::class)->make([
            'url' => 'http://new.test',
        ])->toArray();

        $this->json('PATCH', 'api/hooks/'.$hook->id, $data)
            ->assertOk()
            ->assertJson([
                'data' => $data,
            ]);

        $this->assertDatabaseHas('hooks', $data);
    }

    /**
     * @return void
     */
    public function testUpdateDuplicate()
    {
        Sanctum::actingAs($this->user, [
            // TODO
        ]);

        /** @var Team $team */
        $team = factory(Team::class)->create();

        /** @var Project $project */
        $project = $team->projects()->save(factory(Project::class)->make());

        /** @var Collection $hooks */
        $hooks = $project->keys()->saveMany(factory(Hook::class, 2)->make());

        $data = factory(Hook::class)->make([
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
            // TODO
        ]);

        /** @var Team $team */
        $team = factory(Team::class)->create();

        /** @var Project $project */
        $project = $team->projects()->save(factory(Project::class)->make());

        /** @var Hook $hook */
        $hook = $project->hooks()->save(factory(Hook::class)->make());

        $this->json('DELETE', 'api/hooks/'.$hook->id)
            ->assertNoContent();

        $this->assertDeleted($hook);
    }
}
