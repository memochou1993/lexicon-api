<?php

namespace Tests\Feature\Api;

use App\Enums\ErrorType;
use App\Enums\PermissionType;
use App\Models\Project;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
        Sanctum::actingAs($this->user, [
            PermissionType::PROJECT_UPDATE,
        ]);

        /** @var Team $team */
        $team = Team::factory()->create();

        /** @var Project $project */
        $project = $team->projects()->save(Project::factory()->make());

        /** @var User $user */
        $user = User::factory()->create();

        $this->assertCount(1, $project->users);

        $this->json('POST', 'api/projects/'.$project->id.'/users', [
            'user_ids' => $user->id,
        ])
            ->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        $this->assertCount(2, $project->refresh()->users);
    }

    /**
     * @return void
     */
    public function testDetach()
    {
        Sanctum::actingAs($this->user, [
            PermissionType::PROJECT_UPDATE,
        ]);

        /** @var Team $team */
        $team = Team::factory()->create();

        /** @var Project $project */
        $project = $team->projects()->save(Project::factory()->make());

        /** @var User $user */
        $user = $project->users()->save(User::factory()->make());

        $this->assertCount(2, $project->users);

        $this->json('DELETE', 'api/projects/'.$project->id.'/users/'.$user->id)
            ->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        $this->assertCount(1, $project->refresh()->users);
    }

    /**
     * @return void
     */
    public function testGuestAttach()
    {
        Sanctum::actingAs($this->user, [
            PermissionType::PROJECT_UPDATE,
        ]);

        $this->flushEventListeners(Project::class);

        /** @var Team $team */
        $team = Team::factory()->create();

        /** @var Project $project */
        $project = $team->projects()->save(Project::factory()->make());

        $response = $this->json('POST', 'api/projects/'.$project->id.'/users')
            ->assertForbidden();

        $this->assertEquals(
            ErrorType::USER_NOT_IN_PROJECT,
            $response->exception->getCode()
        );
    }

    /**
     * @return void
     */
    public function testGuestDetach()
    {
        /** @var User $user */
        $user = Sanctum::actingAs($this->user, [
            PermissionType::PROJECT_UPDATE,
        ]);

        $this->flushEventListeners(Project::class);

        /** @var Team $team */
        $team = Team::factory()->create();

        /** @var Project $project */
        $project = $team->projects()->save(Project::factory()->make());

        $response = $this->json('DELETE', 'api/projects/'.$project->id.'/users/'.$user->id)
            ->assertForbidden();

        $this->assertEquals(
            ErrorType::USER_NOT_IN_PROJECT,
            $response->exception->getCode()
        );
    }

    /**
     * @return void
     */
    public function testAttachWithoutPermission()
    {
        Sanctum::actingAs($this->user);

        /** @var Team $team */
        $team = Team::factory()->create();

        /** @var Project $project */
        $project = $team->projects()->save(Project::factory()->make());

        $response = $this->json('POST', 'api/projects/'.$project->id.'/users')
            ->assertForbidden();

        $this->assertEquals(
            ErrorType::PERMISSION_DENIED,
            $response->exception->getCode()
        );
    }

    /**
     * @return void
     */
    public function testDetachWithoutPermission()
    {
        /** @var User $user */
        $user = Sanctum::actingAs($this->user);

        /** @var Team $team */
        $team = Team::factory()->create();

        /** @var Project $project */
        $project = $team->projects()->save(Project::factory()->make());

        $response = $this->json('DELETE', 'api/projects/'.$project->id.'/users/'.$user->id)
            ->assertForbidden();

        $this->assertEquals(
            ErrorType::PERMISSION_DENIED,
            $response->exception->getCode()
        );
    }
}
