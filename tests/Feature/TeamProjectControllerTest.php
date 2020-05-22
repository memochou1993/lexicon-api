<?php

namespace Tests\Feature;

use App\Enums\PermissionType;
use App\Models\Project;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TeamProjectControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function testIndex()
    {
        $user = Sanctum::actingAs($this->user, [PermissionType::TEAM_VIEW]);

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
            ])
            ->assertJson([
                'data' => $team->projects->toArray(),
            ]);
    }

    /**
     * @return void
     */
    public function testStore()
    {
        $user = Sanctum::actingAs($this->user, [PermissionType::TEAM_UPDATE]);
        $team = $user->teams()->save(factory(Team::class)->make());

        $data = factory(Project::class)->make()->toArray();

        $this->json('POST', 'api/teams/'.$team->id.'/projects', $data)
            ->assertCreated()
            ->assertJson([
                'data' => $data,
            ]);

        $this->assertDatabaseHas('projects', $data);

        $this->assertCount(1, $team->projects);
    }

    /**
     * @return void
     */
    public function testStoreDuplicate()
    {
        $user = Sanctum::actingAs($this->user, [PermissionType::TEAM_UPDATE]);

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

        $this->assertCount(1, $team->projects);
    }

    /**
     * @return void
     */
    public function testGuestViewAll()
    {
        Sanctum::actingAs($this->user, [PermissionType::TEAM_VIEW]);

        $team = factory(Team::class)->create();
        $team->projects()->save(factory(Project::class)->make());

        $this->json('GET', 'api/teams/'.$team->id.'/projects')
            ->assertForbidden();
    }

    /**
     * @return void
     */
    public function testGuestCreate()
    {
        Sanctum::actingAs($this->user, [PermissionType::TEAM_UPDATE]);

        $team = factory(Team::class)->create();

        $data = factory(Project::class)->make()->toArray();

        $this->json('POST', 'api/teams/'.$team->id.'/projects', $data)
            ->assertForbidden();
    }

    /**
     * @return void
     */
    public function testViewAllWithoutPermission()
    {
        $user = Sanctum::actingAs($this->user);

        $team = $user->teams()->save(factory(Team::class)->make());
        $team->projects()->save(factory(Project::class)->make());

        $this->json('GET', 'api/teams/'.$team->id.'/projects')
            ->assertForbidden();
    }

    /**
     * @return void
     */
    public function testCreateWithoutPermission()
    {
        $user = Sanctum::actingAs($this->user);

        $team = $user->teams()->save(factory(Team::class)->make());

        $data = factory(Project::class)->make()->toArray();

        $this->json('POST', 'api/teams/'.$team->id.'/projects', $data)
            ->assertForbidden();
    }
}
