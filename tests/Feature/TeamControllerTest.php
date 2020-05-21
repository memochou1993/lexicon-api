<?php

namespace Tests\Feature;

use App\Models\Form;
use App\Models\Language;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class TeamControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function testShow()
    {
        $user = Sanctum::actingAs($this->user, ['view-team']);

        $team = $user->teams()->save(factory(Team::class)->make());

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
    public function testUpdate()
    {
        $user = Sanctum::actingAs($this->user, ['update-team']);

        $team = $user->teams()->save(factory(Team::class)->make());

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
        $user = Sanctum::actingAs($this->user, ['update-team']);

        $teams = $user->teams()->saveMany(factory(Team::class, 2)->make());

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
    public function testDestroy()
    {
        $user = Sanctum::actingAs($this->user, ['delete-team']);

        $team = $user->teams()->save(factory(Team::class)->make());
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
    public function testViewWithoutPermission()
    {
        $user = Sanctum::actingAs($this->user);

        $team = $user->teams()->save(factory(Team::class)->make());

        $this->json('GET', 'api/teams/'.$team->id)
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * @return void
     */
    public function testUpdateWithoutPermission()
    {
        $user = Sanctum::actingAs($this->user);

        $team = $user->teams()->save(factory(Team::class)->make());

        $this->json('PATCH', 'api/teams/'.$team->id)
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * @return void
     */
    public function testDeleteWithoutPermission()
    {
        $user = Sanctum::actingAs($this->user);

        $team = $user->teams()->save(factory(Team::class)->make());

        $this->json('DELETE', 'api/teams/'.$team->id)
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }
}
