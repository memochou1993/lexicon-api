<?php

namespace Tests\Feature;

use App\Models\Form;
use App\Models\Key;
use App\Models\Language;
use App\Models\Project;
use App\Models\Team;
use App\Models\User;
use App\Models\Value;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ValueControllerTest extends TestCase
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
        $project = $team->projects()->save(factory(Project::class)->make());
        $key = $project->keys()->save(factory(Key::class)->make());
        $value = $key->values()->save(factory(Value::class)->make());

        $this->json('GET', 'api/values/1')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data' => [
                    'language',
                    'form',
                ],
            ])
            ->assertJson([
                'data' => $value->toArray(),
            ]);
    }

    /**
     * @return void
     */
    public function testUpdate()
    {
        $team = $this->user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->make());
        $key = $project->keys()->save(factory(Key::class)->make());
        $key->values()->save(factory(Value::class)->make());

        $value = factory(Value::class)->make([
            'text' => 'New Value',
        ])->toArray();

        $this->json('PATCH', 'api/values/1', $value)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'data' => $value,
            ]);

        $this->assertDatabaseHas('values', $value);

        $this->assertCount(1, $key->values);
    }

    /**
     * @return void
     */
    public function testDestroy()
    {
        $team = $this->user->teams()->save(factory(Team::class)->make());
        $language = $team->languages()->save(factory(Language::class)->make());
        $form = $team->forms()->save(factory(Form::class)->make());
        $language->forms()->attach($form->id);
        $project = $team->projects()->save(factory(Project::class)->make());
        $project->languages()->attach($language->id);
        $key = $project->keys()->save(factory(Key::class)->make());
        $value = $key->values()->save(factory(Value::class)->make());
        $value->languages()->attach($language->id);
        $value->forms()->attach($form->id);

        $this->assertCount(1, $value->languages);
        $this->assertCount(1, $value->forms);

        $this->json('DELETE', 'api/values/1')
            ->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDeleted($value);

        $this->assertDatabaseMissing('model_has_languages', [
            'language_id' => $language->id,
            'model_type' => 'value',
            'model_id' => $value->id,
        ]);

        $this->assertDatabaseMissing('model_has_forms', [
            'form_id' => $form->id,
            'model_type' => 'value',
            'model_id' => $value->id,
        ]);
    }
}
