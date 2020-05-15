<?php

namespace Tests\Feature;

use App\Models\Form;
use App\Models\Language;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class TeamControllerTest extends TestCase
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
    public function testIndex()
    {
        $team = $this->user->teams()->save(factory(Team::class)->make());

        $this->json('GET', 'api/teams', [
            'relations' => 'users,projects,languages,forms',
        ])
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data' => [
                    [
                        'users',
                        'projects',
                        'languages',
                        'forms',
                    ],
                ],
            ])
            ->assertJson([
                'data' => [
                    $team->toArray(),
                ],
            ]);
    }

    /**
     * @return void
     */
    public function testStore()
    {
        $team = factory(Team::class)
            ->make()
            ->toArray();

        $this->json('POST', 'api/teams', $team)
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJson([
                'data' => $team,
            ]);

        $this->assertDatabaseHas('teams', $team);

        $this->assertCount(1, $this->user->teams);
    }

    /**
     * @return void
     */
    public function testStoreDuplicate()
    {
        $this->user->teams()->save(factory(Team::class)->make([
            'name' => 'Unique Team',
        ]));

        $team = factory(Team::class)
            ->make([
                'name' => 'Unique Team',
            ])
            ->toArray();

        $this->json('POST', 'api/teams', $team)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure([
                'errors' => [
                    'name',
                ],
            ]);

        $this->assertCount(1, $this->user->teams);
    }

    /**
     * @return void
     */
    public function testShow()
    {
        $team = $this->user->teams()->save(factory(Team::class)->make());

        $this->json('GET', 'api/teams/1', [
            'relations' => 'users,projects,languages,forms',
        ])
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data' => [
                    'users',
                    'projects',
                    'languages',
                    'forms',
                ],
            ])
            ->assertJson([
                'data' => $team->toArray(),
            ]);
    }

    /**
     * @return void
     */
    public function testUpdate()
    {
        $this->user->teams()->save(factory(Team::class)->make());

        $team = factory(Team::class)
            ->make([
                'name' => 'New Team',
            ])
            ->toArray();

        $this->json('PATCH', 'api/teams/1', $team)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'data' => $team,
            ]);

        $this->assertDatabaseHas('teams', $team);
    }

    /**
     * @return void
     */
    public function testUpdateDuplicate()
    {
        $this->user->teams()->saveMany(factory(Team::class, 2)->make());

        $team = factory(Team::class)
            ->make([
                'name' => 'New Team 1',
            ])
            ->toArray();

        $this->json('PATCH', 'api/teams/1', $team)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'data' => $team,
            ]);

        $team = factory(Team::class)
            ->make([
                'name' => 'Team 2',
            ])
            ->toArray();

        $this->json('PATCH', 'api/teams/1', $team)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure([
                'errors' => [
                    'name',
                ],
            ]);
    }

    /**
     * @return void
     */
    public function testDestroy()
    {
        $team = $this->user->teams()->save(factory(Team::class)->make());
        $language = $team->languages()->save(factory(Language::class)->make());
        $form = $team->forms()->save(factory(Form::class)->make());

        $this->assertCount(1, $team->users);
        $this->assertCount(1, $team->languages);
        $this->assertCount(1, $team->forms);

        $this->json('DELETE', 'api/teams/1')
            ->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDeleted($team);

        $this->assertDatabaseMissing('model_has_users', [
            'user_id' => $this->user->id,
            'model_type' => 'team',
            'model_id' => $team->id,
        ]);

        $this->assertDatabaseMissing('model_has_languages', [
            'language_id' => $language->id,
            'model_type' => 'team',
            'model_id' => $team->id,
        ]);

        $this->assertDatabaseMissing('model_has_forms', [
            'form_id' => $form->id,
            'model_type' => 'team',
            'model_id' => $team->id,
        ]);
    }
}
