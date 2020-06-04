<?php

namespace Tests\Feature\Api\Client;

use App\Models\Project;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Cache;
use Laravel\Sanctum\Sanctum;
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
        $team = factory(Team::class)->create();

        /** @var Project $project */
        $project = $team->projects()->save(factory(Project::class)->make());

        Sanctum::actingAs($project);

        Cache::shouldReceive('forget')->once()->andReturn(true);

        $this->json('DELETE', 'api/client/project/cache')
            ->assertOk()
            ->assertJson([
                'success' => true,
            ]);
    }
}
