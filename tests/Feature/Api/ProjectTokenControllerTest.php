<?php

namespace Tests\Feature\Api;

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

    // TODO: test guest
    // TODO: test without permission
}
