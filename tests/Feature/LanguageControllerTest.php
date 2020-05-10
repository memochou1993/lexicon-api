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
    public function testIndex()
    {
        $team = $this->user->teams()->save(factory(Team::class)->make());
        $team->languages()->save(factory(Language::class)->make());

        $this->json('GET', 'api/languages', [
            'team_id' => $team->id,
            'relations' => 'forms',
        ])
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data' => [
                    [
                        'forms',
                    ],
                ],
            ])
            ->assertJson([
                'data' => $team->languages->toArray(),
            ]);
    }

    /**
     * @return void
     */
    public function testStore()
    {
        $team = $this->user->teams()->save(factory(Team::class)->make());

        $language = factory(Language::class)->make([
            'team_id' => 1,
        ]);

        $this->json('POST', 'api/languages', $language->toArray())
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJson([
                'data' => $language->makeHidden('team_id')->toArray(),
            ]);

        $this->assertDatabaseHas('languages', $language->toArray());

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

        $language = factory(Language::class)
            ->make([
                'name' => 'Unique Language',
                'team_id' => $team->id,
            ])
            ->toArray();

        $this->json('POST', 'api/languages', $language)
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

        $this->assertDatabaseHas('languages', $language);
    }

    /**
     * @return void
     */
    public function testDestroy()
    {
        $team = $this->user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->make());
        $language = $project->languages()->save(factory(Language::class)->make());
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
}
