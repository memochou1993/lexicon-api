<?php

namespace Tests\Feature\Api;

use App\Enums\ErrorType;
use App\Enums\PermissionType;
use App\Models\Project;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProjectTokenControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function testStore()
    {
        $user = Sanctum::actingAs($this->user, [PermissionType::PROJECT_UPDATE]);

        $team = $user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->make());

        $this->assertCount(0, $project->tokens);

        $this->json('POST', 'api/projects/'.$project->id.'/tokens')
            ->assertOk()
            ->assertJsonStructure([
                'access_token',
            ]);

        $this->assertCount(1, $project->refresh()->tokens);
    }

    /**
     * @return void
     */
    public function testDestroy()
    {
        $user = Sanctum::actingAs($this->user, [PermissionType::PROJECT_UPDATE]);

        $team = $user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->make());
        $token = $project->createToken('')->accessToken;

        $this->assertCount(1, $project->refresh()->tokens);

        $this->json('DELETE', 'api/projects/'.$project->id.'/tokens/'.$token->id)
            ->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        $this->assertCount(0, $project->refresh()->tokens);
    }

    /**
     * @return void
     */
    public function testGuestStore()
    {
        $user = Sanctum::actingAs($this->user, [PermissionType::PROJECT_UPDATE]);

        $team = $user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->disableEvents()->make());

        $response = $this->json('POST', 'api/projects/'.$project->id.'/tokens')
            ->assertForbidden();

        $this->assertEquals(
            ErrorType::USER_NOT_IN_PROJECT,
            $response->exception->getCode()
        );
    }

    /**
     * @return void
     */
    public function testGuestDestroy()
    {
        $user = Sanctum::actingAs($this->user, [PermissionType::PROJECT_UPDATE]);

        $team = $user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->disableEvents()->make());
        $token = $project->createToken('')->accessToken;

        $response = $this->json('DELETE', 'api/projects/'.$project->id.'/tokens/'.$token->id)
            ->assertForbidden();

        $this->assertEquals(
            ErrorType::USER_NOT_IN_PROJECT,
            $response->exception->getCode()
        );
    }

    /**
     * @return void
     */
    public function testStoreWithoutPermission()
    {
        $user = Sanctum::actingAs($this->user);

        $team = $user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->make());

        $response = $this->json('POST', 'api/projects/'.$project->id.'/tokens')
            ->assertForbidden();

        $this->assertEquals(
            ErrorType::PERMISSION_DENIED,
            $response->exception->getCode()
        );
    }

    /**
     * @return void
     */
    public function testDestroyWithoutPermission()
    {
        $user = Sanctum::actingAs($this->user);

        $team = $user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->make());
        $token = $project->createToken('')->accessToken;

        $response = $this->json('DELETE', 'api/projects/'.$project->id.'/tokens/'.$token->id)
            ->assertForbidden();

        $this->assertEquals(
            ErrorType::PERMISSION_DENIED,
            $response->exception->getCode()
        );
    }
}
