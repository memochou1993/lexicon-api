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

        $this->json('GET', 'api/languages/1', [
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
        $team->languages()->save(factory(Language::class)->make());

        $this->json('GET', 'api/languages/1')
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * @return void
     */
    public function testUpdate()
    {
        $team = $this->user->teams()->save(factory(Team::class)->make());
        $team->languages()->save(factory(Language::class)->make());

        $language = factory(Language::class)->make([
            'name' => 'New Language',
        ])->toArray();

        $this->json('PATCH', 'api/languages/1', $language)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'data' => $language,
            ]);

        $this->assertDatabaseHas('languages', $language);
    }

    /**
     * @return void
     */
    public function testUpdateDuplicate()
    {
        $team = $this->user->teams()->save(factory(Team::class)->make());
        $team->languages()->saveMany(factory(Language::class, 2)->make());

        $language = factory(Language::class)->make([
            'name' => 'New Language 1',
        ])->toArray();

        $this->json('PATCH', 'api/languages/1', $language)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'data' => $language,
            ]);

        $language = factory(Language::class)->make([
            'name' => 'Language 2',
        ])->toArray();

        $this->json('PATCH', 'api/languages/1', $language)
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
        $team->languages()->save(factory(Language::class)->make());

        $this->json('PATCH', 'api/languages/1')
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

        $this->json('DELETE', 'api/languages/1')
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
        $team->languages()->save(factory(Language::class)->make());

        $this->json('DELETE', 'api/languages/1')
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }
}
