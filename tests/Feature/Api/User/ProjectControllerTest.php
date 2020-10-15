<?php

namespace Tests\Feature\Api\User;

use App\Models\Project;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProjectControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function testIndex()
    {
        Sanctum::actingAs($this->user);

        /** @var Team $team */
        $team = Team::factory()->create();
        $team->projects()->save(Project::factory()->make());

        $this->json('GET', 'api/user/projects', [
            'relations' => 'users,languages',
        ])
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    [
                        'owner',
                        'users',
                        'languages',
                    ],
                ],
            ]);
    }
}
