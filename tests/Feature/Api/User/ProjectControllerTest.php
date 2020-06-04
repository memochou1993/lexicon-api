<?php

namespace Tests\Feature\Api\User;

use App\Models\Project;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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
        $team = factory(Team::class)->create();
        $team->projects()->save(factory(Project::class)->make());

        $this->json('GET', 'api/user/projects', [
            'relations' => 'users,languages',
        ])
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    [
                        'users',
                        'languages',
                    ],
                ],
            ]);
    }
}
