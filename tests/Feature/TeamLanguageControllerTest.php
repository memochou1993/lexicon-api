<?php

namespace Tests\Feature;

use App\Models\Language;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TeamLanguageControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function testStore()
    {
        $user = Sanctum::actingAs($this->user, ['update-team']);

        $team = $user->teams()->save(factory(Team::class)->make());

        $data = factory(Language::class)->make()->toArray();

        $this->json('POST', 'api/teams/'.$team->id.'/languages', $data)
            ->assertCreated()
            ->assertJson([
                'data' => $data,
            ]);

        $this->assertDatabaseHas('languages', $data);

        $this->assertCount(1, $team->languages);
    }

    /**
     * @return void
     */
    public function testStoreDuplicate()
    {
        $user = Sanctum::actingAs($this->user, ['update-team']);

        $team = $user->teams()->save(factory(Team::class)->make());
        $team->languages()->save(factory(Language::class)->make([
            'name' => 'Unique Language',
        ]));

        $data = factory(Language::class)->make([
            'name' => 'Unique Language',
        ])->toArray();

        $this->json('POST', 'api/teams/'.$team->id.'/languages', $data)
            ->assertJsonValidationErrors([
                'name',
            ]);

        $this->assertCount(1, $team->languages);
    }

    /**
     * @return void
     */
    public function testCreateWithoutPermission()
    {
        $user = Sanctum::actingAs($this->user);

        $team = $user->teams()->save(factory(Team::class)->make());

        $data = factory(Language::class)->make()->toArray();

        $this->json('POST', 'api/teams/'.$team->id.'/languages', $data)
            ->assertForbidden();
    }
}
