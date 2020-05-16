<?php

namespace Tests\Feature;

use App\Models\Form;
use App\Models\Language;
use App\Models\Project;
use App\Models\Team;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LanguageControllerTest extends TestCase
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
    public function testShow()
    {
        $team = $this->user->teams()->save(factory(Team::class)->make());
        $language = $team->languages()->save(factory(Language::class)->make());

        $this->json('GET', 'api/languages/'.$language->id, [
            'relations' => 'forms',
        ])
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data' => [
                    'forms',
                ],
            ])
            ->assertJson([
                'data' => $language->toArray(),
            ]);
    }

    /**
     * @return void
     */
    public function testViewForbidden()
    {
        $guest = factory(User::class)->create();
        $team = $guest->teams()->save(factory(Team::class)->make());
        $language = $team->languages()->save(factory(Language::class)->make());

        $this->json('GET', 'api/languages/'.$language->id)
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * @return void
     */
    public function testUpdate()
    {
        $team = $this->user->teams()->save(factory(Team::class)->make());
        $language = $team->languages()->save(factory(Language::class)->make());

        $data = factory(Language::class)->make([
            'name' => 'New Language',
        ])->toArray();

        $this->json('PATCH', 'api/languages/'.$language->id, $data)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'data' => $data,
            ]);

        $this->assertDatabaseHas('languages', $data);
    }

    /**
     * @return void
     */
    public function testUpdateDuplicate()
    {
        $team = $this->user->teams()->save(factory(Team::class)->make());
        $languages = $team->languages()->saveMany(factory(Language::class, 2)->make());

        $data = factory(Language::class)->make([
            'name' => $languages->last()->name,
        ])->toArray();

        $this->json('PATCH', 'api/languages/'.$languages->first()->id, $data)
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
        $guest = factory(User::class)->create();
        $team = $guest->teams()->save(factory(Team::class)->make());
        $language = $team->languages()->save(factory(Language::class)->make());

        $this->json('PATCH', 'api/languages/'.$language->id)
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * @return void
     */
    public function testDestroy()
    {
        $team = $this->user->teams()->save(factory(Team::class)->make());
        $language = $team->languages()->save(factory(Language::class)->make());
        $form = $language->forms()->save(factory(Form::class)->make());

        $this->json('DELETE', 'api/languages/'.$language->id)
            ->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDeleted($language);

        $this->assertDatabaseMissing('model_has_forms', [
            'form_id' => $form->id,
            'model_type' => 'language',
            'model_id' => $language->id,
        ]);
    }

    /**
     * @return void
     */
    public function testDeleteForbidden()
    {
        $guest = factory(User::class)->create();
        $team = $guest->teams()->save(factory(Team::class)->make());
        $language = $team->languages()->save(factory(Language::class)->make());

        $this->json('DELETE', 'api/languages/'.$language->id)
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }
}
