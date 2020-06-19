<?php

namespace Tests\Feature\Api\Client;

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
        $team = factory(Team::class)->create();

        /** @var Project $project */
        $project = $team->projects()->save(factory(Project::class)->make());

        Cache::shouldReceive('forget')->once()->andReturn(true);

        $this
            ->withHeaders([
                'X-Lexicon-API-Key' => $project->getSetting('api_key'),
            ])
            ->json('DELETE', 'api/client/projects/'.$project->id.'/cache')
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
        /** @var Team $team */
        $team = factory(Team::class)->create();

        /** @var Project $project */
        $project = $team->projects()->save(factory(Project::class)->make());

        $this->json('DELETE', 'api/client/projects/'.$project->id.'/cache')
            ->assertUnauthorized();
    }
}
