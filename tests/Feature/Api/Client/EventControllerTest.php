<?php

namespace Tests\Feature\Api\Client;

use App\Models\Hook;
use App\Models\Project;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class EventControllerTest extends TestCase
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

        $this->withHeaders([
                'X-Lexicon-API-Key' => $project->getSetting('api_key'),
            ])
            ->json('POST', 'api/client/projects/'.$project->id.'/dispatch')
            ->assertStatus(Response::HTTP_ACCEPTED);
    }
}
