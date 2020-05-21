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

        $this->user = $this->actingAsRole('admin');
    }

    /**
     * @return void
     */
    public function testShow()
    {
        $team = $this->user->teams()->save(factory(Team::class)->make());

        $this->json('GET', 'api/teams/'.$team->id, [
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
    public function testViewForbidden()
    {
        $user = factory(User::class)->create();
        $team = $user->teams()->save(factory(Team::class)->make());

        $this->json('GET', 'api/teams/'.$team->id)
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * @return void
     */
    public function testUpdate()
    {
        $team = $this->user->teams()->save(factory(Team::class)->make());

        $data = factory(Team::class)->make([
            'name' => 'New Team',
        ])->toArray();

        $this->json('PATCH', 'api/teams/'.$team->id, $data)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'data' => $data,
            ]);

        $this->assertDatabaseHas('teams', $data);
    }

    /**
     * @return void
     */
    public function testUpdateDuplicate()
    {
        $teams = $this->user->teams()->saveMany(factory(Team::class, 2)->make());

        $data = factory(Team::class)->make([
            'name' => $teams->last()->name,
        ])->toArray();

        $this->json('PATCH', 'api/teams/'.$teams->first()->id, $data)
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
    public function testUpdateForbidden()
    {
        $user = factory(User::class)->create();
        $team = $user->teams()->save(factory(Team::class)->make());

        $this->json('PATCH', 'api/teams/'.$team->id)
            ->assertStatus(Response::HTTP_FORBIDDEN);
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

        $this->json('DELETE', 'api/teams/'.$team->id)
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

    /**
     * @return void
     */
    public function testDeleteForbidden()
    {
        $user = factory(User::class)->create();
        $team = $user->teams()->save(factory(Team::class)->make());

        $this->json('DELETE', 'api/teams/'.$team->id)
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }
}
