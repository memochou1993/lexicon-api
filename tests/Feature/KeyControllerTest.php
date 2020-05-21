<?php

namespace Tests\Feature;

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
    public function testShow()
    {
        $user = Sanctum::actingAs($this->user, ['view-key']);

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
        $user = Sanctum::actingAs($this->user, ['update-key']);

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
        $user = Sanctum::actingAs($this->user, ['update-key']);

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
        $user = Sanctum::actingAs($this->user, ['delete-key']);

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
    public function testViewForbidden()
    {
        $user = Sanctum::actingAs($this->user, ['view-key']);

        $team = $user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->withoutEvents()->make());
        $key = $project->keys()->save(factory(Key::class)->make());

        $this->json('GET', 'api/keys/'.$key->id)
            ->assertForbidden();
    }

    /**
     * @return void
     */
    public function testUpdateForbidden()
    {
        $user = Sanctum::actingAs($this->user, ['update-key']);

        $team = $user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->withoutEvents()->make());
        $key = $project->keys()->save(factory(Key::class)->make());

        $this->json('PATCH', 'api/keys/'.$key->id)
            ->assertForbidden();
    }
    /**
     * @return void
     */
    public function testDeleteForbidden()
    {
        $user = Sanctum::actingAs($this->user, ['delete-key']);

        $team = $user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->withoutEvents()->make());
        $key = $project->keys()->save(factory(Key::class)->make());

        $this->json('DELETE', 'api/keys/'.$key->id)
            ->assertForbidden();
    }
}
