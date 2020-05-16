<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ProjectUserControllerTest extends TestCase
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
    public function testAttach()
    {
        $team = $this->user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->make());
        $user = factory(User::class)->create();

        $this->assertCount(1, $project->users);

        $this->json('POST', 'api/projects/'.$project->id.'/users', [
            'user_ids' => $user->id,
        ])
            ->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertCount(2, $project->refresh()->users);
    }

    /**
     * @return void
     */
    public function testSync()
    {
        $team = $this->user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->make());
        $project->users()->save(factory(User::class)->make());

        $this->assertCount(2, $project->users);

        $this->json('POST', 'api/projects/'.$project->id.'/users', [
            'user_ids' => $this->user->id,
            'sync' => true,
        ])
            ->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertCount(1, $project->refresh()->users);
    }

    /**
     * @return void
     */
    public function testDetach()
    {
        $team = $this->user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->make());
        $user = $project->users()->save(factory(User::class)->make());

        $this->assertCount(2, $project->users);

        $this->json('DELETE', 'api/projects/'.$project->id.'/users/'.$user->id)
            ->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertCount(1, $project->refresh()->users);
    }
}
