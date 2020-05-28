<?php

namespace Tests\Feature\Api\Client;

use App\Models\Key;
use App\Models\Project;
use App\Models\Team;
use App\Models\Value;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class KeyControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function testIndex()
    {
        $user = $this->user;

        $team = $user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->make());
        $key = $project->keys()->save(factory(Key::class)->make());
        $key->values()->save(factory(Value::class)->make());

        $this->json('GET', 'api/client/projects/'.$project->id.'/keys', [], [
            'X-Localize-Secret-Key' => json_decode($project->api_keys)->secret_key,
        ])
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    [
                        'values',
                    ],
                ],
            ]);
    }

    /**
     * @return void
     */
    public function testViewAllWithoutSecretKey()
    {
        $user = $this->user;

        $team = $user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->make());
        $key = $project->keys()->save(factory(Key::class)->make());
        $key->values()->save(factory(Value::class)->make());

        $this->json('GET', 'api/client/projects/'.$project->id.'/keys')
            ->assertUnauthorized();
    }
}
