<?php

namespace Tests\Feature\Api;

use App\Enums\ErrorType;
use App\Enums\PermissionType;
use App\Models\Form;
use App\Models\Key;
use App\Models\Language;
use App\Models\Project;
use App\Models\Team;
use App\Models\Value;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class LanguageControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function testStore()
    {
        Sanctum::actingAs($this->user, [
            PermissionType::TEAM_VIEW,
            PermissionType::LANGUAGE_CREATE,
        ]);

        /** @var Team $team */
        $team = factory(Team::class)->create();
        $form_ids = factory(Form::class, 2)->create()->pluck('id')->toArray();

        $data = factory(Language::class)->make([
            'form_ids' => $form_ids,
        ]);

        $response = $this->json('POST', 'api/teams/'.$team->id.'/languages', $data->toArray())
            ->assertCreated()
            ->assertJson([
                'data' => $data->makeHidden('form_ids')->toArray(),
            ]);

        $this->assertDatabaseHas('languages', $data->toArray());

        $this->assertCount(1, $team->refresh()->languages);

        /** @var Language $language */
        $language = Language::query()->find(json_decode($response->getContent())->data->id);

        $this->assertCount(
            count($form_ids),
            $language->forms
        );
    }

    /**
     * @return void
     */
    public function testStoreDuplicate()
    {
        Sanctum::actingAs($this->user, [
            PermissionType::TEAM_VIEW,
            PermissionType::LANGUAGE_CREATE,
        ]);

        /** @var Team $team */
        $team = factory(Team::class)->create();
        $team->languages()->save(factory(Language::class)->make([
            'name' => 'Unique Language',
        ]));

        $data = factory(Language::class)->make([
            'name' => 'Unique Language',
        ])->toArray();

        $this->json('POST', 'api/teams/'.$team->id.'/languages', $data)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors([
                'name',
            ]);

        $this->assertCount(1, $team->refresh()->languages);
    }

    /**
     * @return void
     */
    public function testShow()
    {
        Sanctum::actingAs($this->user, [
            PermissionType::LANGUAGE_VIEW,
        ]);

        /** @var Team $team */
        $team = factory(Team::class)->create();

        /** @var Language $language */
        $language = $team->languages()->save(factory(Language::class)->make());

        $this->json('GET', 'api/languages/'.$language->id, [
            'relations' => 'forms',
        ])
            ->assertOk()
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
        Sanctum::actingAs($this->user, [
            PermissionType::LANGUAGE_UPDATE,
        ]);

        /** @var Team $team */
        $team = factory(Team::class)->create();

        /** @var Language $language */
        $language = $team->languages()->save(factory(Language::class)->make());

        $data = factory(Language::class)->make([
            'name' => 'New Language',
        ]);

        $this->json('PATCH', 'api/languages/'.$language->id, $data->toArray())
            ->assertOk()
            ->assertJson([
                'data' => $data->makeHidden('form_ids')->toArray(),
            ]);

        $this->assertDatabaseHas('languages', $data->toArray());
    }

    /**
     * @return void
     */
    public function testAttachForm()
    {
        Sanctum::actingAs($this->user, [
            PermissionType::LANGUAGE_UPDATE,
        ]);

        /** @var Team $team */
        $team = factory(Team::class)->create();

        /** @var Language $language */
        $language = $team->languages()->save(factory(Language::class)->make());

        $form_ids = factory(Form::class, 2)->create()->pluck('id')->toArray();

        $data = factory(Language::class)->make([
            'form_ids' => $form_ids,
        ]);

        $this->json('PATCH', 'api/languages/'.$language->id, $data->toArray())
            ->assertOk()
            ->assertJson([
                'data' => $data->makeHidden('form_ids')->toArray(),
            ]);

        $this->assertCount(count($form_ids), $language->refresh()->forms);
    }

    /**
     * @return void
     */
    public function testDetachForm()
    {
        Sanctum::actingAs($this->user, [
            PermissionType::LANGUAGE_UPDATE,
        ]);

        /** @var Team $team */
        $team = factory(Team::class)->create();

        /** @var Language $language */
        $language = $team->languages()->save(factory(Language::class)->make());

        /** @var Collection $forms */
        $forms = $team->forms()->saveMany(factory(Form::class, 2)->make());
        $language->forms()->attach($forms);

        /** @var Project $project */
        $project = $team->projects()->save(factory(Project::class)->make());
        $project->languages()->attach($language);

        /** @var Key $key */
        $key = $project->keys()->save(factory(Key::class)->make());

        /** @var Collection $values */
        $values = $key->values()->saveMany(factory(Value::class, 2)->make());
        $values->first()->languages()->attach($language);
        $values->first()->forms()->attach($forms->first()->id);
        $values->last()->languages()->attach($language);
        $values->last()->forms()->attach($forms->last()->id);

        $data = factory(Language::class)->make([
            'form_ids' => $forms->first()->id,
        ]);

        $this->json('PATCH', 'api/languages/'.$language->id, $data->toArray())
            ->assertOk()
            ->assertJson([
                'data' => $data->makeHidden('form_ids')->toArray(),
            ]);

        $this->assertDeleted($values->last());

        $this->assertCount(1, $language->refresh()->forms);
        $this->assertCount(1, $language->refresh()->values);
    }

    /**
     * @return void
     */
    public function testUpdateDuplicate()
    {
        Sanctum::actingAs($this->user, [
            PermissionType::LANGUAGE_UPDATE,
        ]);

        /** @var Team $team */
        $team = factory(Team::class)->create();

        /** @var Collection $languages */
        $languages = $team->languages()->saveMany(factory(Language::class, 2)->make());

        $data = factory(Language::class)->make([
            'name' => $languages->last()->name,
        ])->toArray();

        $this->json('PATCH', 'api/languages/'.$languages->first()->id, $data)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors([
                'name',
            ]);
    }

    /**
     * @return void
     */
    public function testDestroy()
    {
        Sanctum::actingAs($this->user, [
            PermissionType::LANGUAGE_DELETE,
        ]);

        /** @var Team $team */
        $team = factory(Team::class)->create();

        /** @var Language $language */
        $language = $team->languages()->save(factory(Language::class)->make());

        /** @var Form $form */
        $form = $team->forms()->save(factory(Form::class)->make());
        $language->forms()->attach($form);

        /** @var Project $project */
        $project = $team->projects()->save(factory(Project::class)->make());
        $project->languages()->attach($language);

        /** @var Key $key */
        $key = $project->keys()->save(factory(Key::class)->make());

        /** @var Value $value */
        $value = $key->values()->save(factory(Value::class)->make());
        $value->languages()->attach($language);
        $value->forms()->attach($form);

        $this->json('DELETE', 'api/languages/'.$language->id)
            ->assertNoContent();

        $this->assertDeleted($language);

        $this->assertDeleted($value);

        $this->assertDatabaseMissing('model_has_forms', [
            'form_id' => $form->id,
            'model_type' => 'language',
            'model_id' => $language->id,
        ]);
    }

    /**
     * @return void
     */
    public function testGuestCreate()
    {
        Sanctum::actingAs($this->user, [
            PermissionType::TEAM_VIEW,
            PermissionType::LANGUAGE_CREATE,
        ]);

        $this->flushEventListeners(Team::class);

        /** @var Team $team */
        $team = factory(Team::class)->create();

        $data = factory(Language::class)->make()->toArray();

        $response = $this->json('POST', 'api/teams/'.$team->id.'/languages', $data)
            ->assertForbidden();

        $this->assertEquals(
            ErrorType::USER_NOT_IN_TEAM,
            $response->exception->getCode()
        );
    }

    /**
     * @return void
     */
    public function testGuestView()
    {
        Sanctum::actingAs($this->user, [
            PermissionType::LANGUAGE_VIEW,
        ]);

        $this->flushEventListeners(Team::class);

        /** @var Team $team */
        $team = factory(Team::class)->create();

        /** @var Language $language */
        $language = $team->languages()->save(factory(Language::class)->make());

        $response = $this->json('GET', 'api/languages/'.$language->id)
            ->assertForbidden();

        $this->assertEquals(
            ErrorType::USER_NOT_IN_TEAM,
            $response->exception->getCode()
        );
    }

    /**
     * @return void
     */
    public function testGuestUpdate()
    {
        Sanctum::actingAs($this->user, [
            PermissionType::LANGUAGE_UPDATE,
        ]);

        $this->flushEventListeners(Team::class);

        /** @var Team $team */
        $team = factory(Team::class)->create();

        /** @var Language $language */
        $language = $team->languages()->save(factory(Language::class)->make());

        $response = $this->json('PATCH', 'api/languages/'.$language->id)
            ->assertForbidden();

        $this->assertEquals(
            ErrorType::USER_NOT_IN_TEAM,
            $response->exception->getCode()
        );
    }

    /**
     * @return void
     */
    public function testGuestDelete()
    {
        Sanctum::actingAs($this->user, [
            PermissionType::LANGUAGE_DELETE,
        ]);

        $this->flushEventListeners(Team::class);

        /** @var Team $team */
        $team = factory(Team::class)->create();

        /** @var Language $language */
        $language = $team->languages()->save(factory(Language::class)->make());

        $response = $this->json('DELETE', 'api/languages/'.$language->id)
            ->assertForbidden();

        $this->assertEquals(
            ErrorType::USER_NOT_IN_TEAM,
            $response->exception->getCode()
        );
    }

    /**
     * @return void
     */
    public function testCreateWithoutPermission()
    {
        Sanctum::actingAs($this->user);

        /** @var Team $team */
        $team = factory(Team::class)->create();

        $data = factory(Language::class)->make()->toArray();

        $response = $this->json('POST', 'api/teams/'.$team->id.'/languages', $data)
            ->assertForbidden();

        $this->assertEquals(
            ErrorType::PERMISSION_DENIED,
            $response->exception->getCode()
        );
    }

    /**
     * @return void
     */
    public function testViewWithoutPermission()
    {
        Sanctum::actingAs($this->user);

        /** @var Team $team */
        $team = factory(Team::class)->create();

        /** @var Language $language */
        $language = $team->languages()->save(factory(Language::class)->make());

        $response = $this->json('GET', 'api/languages/'.$language->id)
            ->assertForbidden();

        $this->assertEquals(
            ErrorType::PERMISSION_DENIED,
            $response->exception->getCode()
        );
    }

    /**
     * @return void
     */
    public function testUpdateWithoutPermission()
    {
        Sanctum::actingAs($this->user);

        /** @var Team $team */
        $team = factory(Team::class)->create();

        /** @var Language $language */
        $language = $team->languages()->save(factory(Language::class)->make());

        $response = $this->json('PATCH', 'api/languages/'.$language->id)
            ->assertForbidden();

        $this->assertEquals(
            ErrorType::PERMISSION_DENIED,
            $response->exception->getCode()
        );
    }

    /**
     * @return void
     */
    public function testDeleteWithoutPermission()
    {
        Sanctum::actingAs($this->user);

        /** @var Team $team */
        $team = factory(Team::class)->create();

        /** @var Language $language */
        $language = $team->languages()->save(factory(Language::class)->make());

        $response = $this->json('DELETE', 'api/languages/'.$language->id)
            ->assertForbidden();

        $this->assertEquals(
            ErrorType::PERMISSION_DENIED,
            $response->exception->getCode()
        );
    }
}
