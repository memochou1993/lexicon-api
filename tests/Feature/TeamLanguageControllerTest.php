<?php

namespace Tests\Feature;

use App\Models\Language;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class TeamLanguageControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var User
     */
    private $user;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->user = Sanctum::actingAs(factory(User::class)->create());
    }

    /**
     * @return void
     */
    public function testStore()
    {
        $team = $this->user->teams()->save(factory(Team::class)->make());

        $data = factory(Language::class)->make()->toArray();

        $this->json('POST', 'api/teams/'.$team->id.'/languages', $data)
            ->assertStatus(Response::HTTP_CREATED)
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
        $team = $this->user->teams()->save(factory(Team::class)->make());
        $team->languages()->save(factory(Language::class)->make([
            'name' => 'Unique Language',
        ]));

        $data = factory(Language::class)->make([
            'name' => 'Unique Language',
        ])->toArray();

        $this->json('POST', 'api/teams/'.$team->id.'/languages', $data)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure([
                'errors' => [
                    'name',
                ],
            ]);

        $this->assertCount(1, $team->languages);
    }

    /**
     * @return void
     */
    public function testCreateForbidden()
    {
        $guest = factory(User::class)->create();
        $team = $guest->teams()->save(factory(Team::class)->make());

        $data = factory(Language::class)->make()->toArray();

        $this->json('POST', 'api/teams/'.$team->id.'/languages', $data)
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }
}
