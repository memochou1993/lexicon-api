<?php

namespace Tests\Feature\Api\Project;

use App\Models\Project;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class ProjectCacheControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function testDestroy()
    {
        /** @var Team $team */
        $team = Team::factory()->create();

        /** @var Project $project */
        $project = $team->projects()->save(Project::factory()->make());

        Cache::shouldReceive('forget')->once()->andReturn(true);

        $this->withToken($project->getSetting('api_key'))
            ->json('DELETE', 'api/project/cache')
            ->assertOk()
            ->assertJson([
                'success' => true,
            ]);
    }

    /**
     * @return void
     */
    public function testUnauthorized()
    {
        $this->json('DELETE', 'api/project/cache')
            ->assertUnauthorized();
    }
}
