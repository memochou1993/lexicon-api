<?php

namespace Tests\Feature;

use App\Models\Key;
use App\Models\Project;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class KeyControllerTest extends TestCase
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
    public function testIndex()
    {
        $team = $this->user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->make());
        $project->keys()->save(factory(Key::class)->make());

        $this->json('GET', 'api/keys', [
            'project_id' => $project->id,
            'relations' => 'values',
        ])
            ->assertStatus(Response::HTTP_OK)
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
        $team = $this->user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->make());

        $key = factory(Key::class)
            ->make([
                'project_id' => $project->id,
            ])
            ->makeVisible('project_id');

        $this->json('POST', 'api/keys', $key->toArray())
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJson([
                'data' => $key->makeHidden('project_id')->toArray(),
            ]);

        $this->assertDatabaseHas('keys', $key->toArray());

        $this->assertCount(1, $project->keys);
    }

    /**
     * @return void
     */
    public function testStoreDuplicate()
    {
        $team = $this->user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->make());
        $project->keys()->save(factory(Key::class)->make([
            'name' => 'Unique Key',
        ]));

        $key = factory(Key::class)
            ->make([
                'name' => 'Unique Key',
                'project_id' => $project->id,
            ])
            ->makeVisible('project_id');

        $this->json('POST', 'api/keys', $key->toArray())
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure([
                'errors' => [
                    'name',
                ],
            ]);

        $this->assertCount(1, $project->keys);
    }

    /**
     * @return void
     */
    public function testShow()
    {
        $team = $this->user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->make());
        $key = $project->keys()->save(factory(Key::class)->make());

        $this->json('GET', 'api/keys/1', [
            'relations' => 'project,values',
        ])
            ->assertStatus(Response::HTTP_OK)
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
        $team = $this->user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->make());
        $project->keys()->save(factory(Key::class)->make());

        $key = factory(Key::class)
            ->make([
                'name' => 'New Key',
            ])
            ->toArray();

        $this->json('PATCH', 'api/keys/1', $key)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'data' => $key,
            ]);

        $this->assertDatabaseHas('keys', $key);
    }

    /**
     * @return void
     */
    public function testUpdateDuplicate()
    {
        $team = $this->user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->make());
        $project->keys()->saveMany(factory(Key::class, 2)->make());

        $key = factory(Key::class)
            ->make([
                'name' => 'New Key 1',
            ])
            ->toArray();

        $this->json('PATCH', 'api/keys/1', $key)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'data' => $key,
            ]);

        $key = factory(Key::class)
            ->make([
                'name' => 'Key 2',
            ])
            ->toArray();

        $this->json('PATCH', 'api/keys/1', $key)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure([
                'errors' => [
                    'name',
                ],
            ]);
    }
}
