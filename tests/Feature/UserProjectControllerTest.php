<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserProjectControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function testIndex()
    {
        $user = Sanctum::actingAs($this->user);

        $team = $user->teams()->save(factory(Team::class)->make());
        $projects =$team->projects()->save(factory(Project::class)->make());

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
            ])
            ->assertJson([
                'data' => [
                    $projects->toArray(),
                ],
            ]);
    }
}
