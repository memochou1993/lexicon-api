<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProjectUserControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function testAttach()
    {
        $user = Sanctum::actingAs($this->user, ['update-project']);

        $team = $user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->make());
        $user = factory(User::class)->create();

        $this->assertCount(1, $project->users);

        $this->json('POST', 'api/projects/'.$project->id.'/users', [
            'user_ids' => $user->id,
        ])
            ->assertNoContent();

        $this->assertCount(2, $project->refresh()->users);
    }

    /**
     * @return void
     */
    public function testSync()
    {
        $user = Sanctum::actingAs($this->user, ['update-project']);

        $team = $user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->make());
        $project->users()->save(factory(User::class)->make());

        $this->assertCount(2, $project->users);

        $this->json('POST', 'api/projects/'.$project->id.'/users', [
            'user_ids' => $user->id,
            'sync' => true,
        ])
            ->assertNoContent();

        $this->assertCount(1, $project->refresh()->users);
    }

    /**
     * @return void
     */
    public function testDetach()
    {
        $user = Sanctum::actingAs($this->user, ['update-project']);

        $team = $user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->make());
        $member = $project->users()->save(factory(User::class)->make());

        $this->assertCount(2, $project->users);

        $this->json('DELETE', 'api/projects/'.$project->id.'/users/'.$member->id)
            ->assertNoContent();

        $this->assertCount(1, $project->refresh()->users);
    }

    /**
     * @return void
     */
    public function testGuestAttach()
    {
        $user = Sanctum::actingAs($this->user, ['update-project']);

        $team = $user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->withoutEvents()->make());

        $this->json('POST', 'api/projects/'.$project->id.'/users')
            ->assertForbidden();
    }

    /**
     * @return void
     */
    public function testGuestDetach()
    {
        $user = Sanctum::actingAs($this->user, ['update-project']);

        $team = $user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->withoutEvents()->make());

        $this->json('DELETE', 'api/projects/'.$project->id.'/users/'.$user->id)
            ->assertForbidden();
    }

    /**
     * @return void
     */
    public function testAttachWithoutPermission()
    {
        $user = Sanctum::actingAs($this->user);

        $team = $user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->make());

        $this->json('POST', 'api/projects/'.$project->id.'/users')
            ->assertForbidden();
    }

    /**
     * @return void
     */
    public function testDetachWithoutPermission()
    {
        $user = Sanctum::actingAs($this->user);

        $team = $user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->make());

        $this->json('DELETE', 'api/projects/'.$project->id.'/users/'.$user->id)
            ->assertForbidden();
    }
}
