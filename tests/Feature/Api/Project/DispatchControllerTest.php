<?php

namespace Tests\Feature\Api\Project;

use App\Models\Hook;
use App\Models\Project;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class DispatchControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function testIndex()
    {
        /** @var Team $team */
        $team = Team::factory()->create();

        /** @var Project $project */
        $project = $team->projects()->save(Project::factory()->make());
        $project->hooks()->save(Hook::factory()->make());

        Http::fake(function () {
            return Http::response(null, Response::HTTP_ACCEPTED);
        });

        $this->withToken($project->getSetting('api_key'))
            ->json('POST', 'api/project/dispatch')
            ->assertStatus(Response::HTTP_ACCEPTED);
    }
}
