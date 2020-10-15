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
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ValueControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function testStore()
    {
        Sanctum::actingAs($this->user, [
            PermissionType::KEY_VIEW,
            PermissionType::VALUE_CREATE,
        ]);

        /** @var Team $team */
        $team = Team::factory()->create();

        /** @var Project $project */
        $project = $team->projects()->save(Project::factory()->make());

        /** @var Language $language */
        $language = $team->languages()->save(Language::factory()->make());
        $project->languages()->attach($language);

        /** @var Form $form */
        $form = $team->forms()->save(Form::factory()->make());
        $language->forms()->attach($form);

        /** @var Key $key */
        $key = $project->keys()->save(Key::factory()->make());

        $value = Value::factory()->make([
            'language_id' => $language->id,
            'form_id' => $form->id,
        ]);

        $this->json('POST', 'api/keys/'.$key->id.'/values', $value->toArray())
            ->assertCreated();

        $this->assertCount(1, $key->refresh()->values);
    }

    /**
     * @return void
     */
    public function testShow()
    {
        Sanctum::actingAs($this->user, [
            PermissionType::VALUE_VIEW,
        ]);

        /** @var Team $team */
        $team = Team::factory()->create();

        /** @var Project $project */
        $project = $team->projects()->save(Project::factory()->make());

        /** @var Language $language */
        $language = $team->languages()->save(Language::factory()->make());
        $project->languages()->attach($language);

        /** @var Form $form */
        $form = $team->forms()->save(Form::factory()->make());
        $language->forms()->attach($form);

        /** @var Key $key */
        $key = $project->keys()->save(Key::factory()->make());

        /** @var Value $value */
        $value = $key->values()->save(Value::factory()->make());
        $value->languages()->attach($language);
        $value->forms()->attach($form);

        $this->json('GET', 'api/values/'.$value->id, [
            'relations' => '',
        ])
            ->assertOk()
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
        Sanctum::actingAs($this->user, [
            PermissionType::VALUE_UPDATE,
        ]);

        /** @var Team $team */
        $team = Team::factory()->create();

        /** @var Project $project */
        $project = $team->projects()->save(Project::factory()->make());

        /** @var Language $language */
        $language = $team->languages()->save(Language::factory()->make());
        $project->languages()->attach($language);

        /** @var Form $form */
        $form = $team->forms()->save(Form::factory()->make());
        $language->forms()->attach($form);

        /** @var Key $key */
        $key = $project->keys()->save(Key::factory()->make());

        /** @var Value $value */
        $value = $key->values()->save(Value::factory()->make());
        $value->languages()->attach($language);
        $value->forms()->attach($form);

        $data = Value::factory()->make([
            'text' => 'New Value',
        ])->toArray();

        $this->json('PATCH', 'api/values/'.$value->id, $data)
            ->assertOk();

        $this->assertCount(1, $key->refresh()->values);
    }

    /**
     * @return void
     */
    public function testDestroy()
    {
        Sanctum::actingAs($this->user, [
            PermissionType::VALUE_DELETE,
        ]);

        /** @var Team $team */
        $team = Team::factory()->create();

        /** @var Project $project */
        $project = $team->projects()->save(Project::factory()->make());

        /** @var Language $language */
        $language = $team->languages()->save(Language::factory()->make());
        $project->languages()->attach($language);

        /** @var Form $form */
        $form = $team->forms()->save(Form::factory()->make());
        $language->forms()->attach($form);

        /** @var Key $key */
        $key = $project->keys()->save(Key::factory()->make());

        /** @var Value $value */
        $value = $key->values()->save(Value::factory()->make());
        $value->languages()->attach($language);
        $value->forms()->attach($form);

        $this->assertCount(1, $value->languages);
        $this->assertCount(1, $value->forms);

        $this->json('DELETE', 'api/values/'.$value->id)
            ->assertNoContent();

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

    /**
     * @return void
     */
    public function testCreateByGuest()
    {
        Sanctum::actingAs($this->user, [
            PermissionType::KEY_VIEW,
            PermissionType::VALUE_CREATE,
        ]);

        $this->flushEventListeners(Project::class);

        /** @var Team $team */
        $team = Team::factory()->create();

        /** @var Language $language */
        $language = $team->languages()->save(Language::factory()->make());

        /** @var Form $form */
        $form = $team->forms()->save(Form::factory()->make());

        /** @var Project $project */
        $project = $team->projects()->save(Project::factory()->make());

        /** @var Key $key */
        $key = $project->keys()->save(Key::factory()->make());

        $value = Value::factory()->make([
            'language_id' => $language->id,
            'form_id' => $form->id,
        ]);

        $response = $this->json('POST', 'api/keys/'.$key->id.'/values', $value->toArray())
            ->assertForbidden();

        $this->assertEquals(
            ErrorType::USER_NOT_IN_PROJECT,
            $response->exception->getCode()
        );
    }

    /**
     * @return void
     */
    public function testViewByGuest()
    {
        Sanctum::actingAs($this->user, [
            PermissionType::VALUE_VIEW,
        ]);

        $this->flushEventListeners(Project::class);

        /** @var Team $team */
        $team = Team::factory()->create();

        /** @var Project $project */
        $project = $team->projects()->save(Project::factory()->make());

        /** @var Key $key */
        $key = $project->keys()->save(Key::factory()->make());

        /** @var Value $value */
        $value = $key->values()->save(Value::factory()->make());

        $response = $this->json('GET', 'api/values/'.$value->id)
            ->assertForbidden();

        $this->assertEquals(
            ErrorType::USER_NOT_IN_PROJECT,
            $response->exception->getCode()
        );
    }

    /**
     * @return void
     */
    public function testUpdateByGuest()
    {
        Sanctum::actingAs($this->user, [
            PermissionType::VALUE_UPDATE,
        ]);

        $this->flushEventListeners(Project::class);

        /** @var Team $team */
        $team = Team::factory()->create();

        /** @var Project $project */
        $project = $team->projects()->save(Project::factory()->make());

        /** @var Key $key */
        $key = $project->keys()->save(Key::factory()->make());

        /** @var Value $value */
        $value = $key->values()->save(Value::factory()->make());

        $response = $this->json('PATCH', 'api/values/'.$value->id)
            ->assertForbidden();

        $this->assertEquals(
            ErrorType::USER_NOT_IN_PROJECT,
            $response->exception->getCode()
        );
    }

    /**
     * @return void
     */
    public function testDeleteByGuest()
    {
        Sanctum::actingAs($this->user, [
            PermissionType::VALUE_DELETE,
        ]);

        $this->flushEventListeners(Project::class);

        /** @var Team $team */
        $team = Team::factory()->create();

        /** @var Project $project */
        $project = $team->projects()->save(Project::factory()->make());

        /** @var Key $key */
        $key = $project->keys()->save(Key::factory()->make());

        /** @var Value $value */
        $value = $key->values()->save(Value::factory()->make());

        $response = $this->json('DELETE', 'api/values/'.$value->id)
            ->assertForbidden();

        $this->assertEquals(
            ErrorType::USER_NOT_IN_PROJECT,
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
        $team = Team::factory()->create();

        /** @var Language $language */
        $language = $team->languages()->save(Language::factory()->make());

        /** @var Form $form */
        $form = $team->forms()->save(Form::factory()->make());

        /** @var Project $project */
        $project = $team->projects()->save(Project::factory()->make());

        /** @var Key $key */
        $key = $project->keys()->save(Key::factory()->make());

        $value = Value::factory()->make([
            'language_id' => $language->id,
            'form_id' => $form->id,
        ]);

        $response = $this->json('POST', 'api/keys/'.$key->id.'/values', $value->toArray())
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
        $team = Team::factory()->create();

        /** @var Project $project */
        $project = $team->projects()->save(Project::factory()->make());

        /** @var Key $key */
        $key = $project->keys()->save(Key::factory()->make());

        /** @var Value $value */
        $value = $key->values()->save(Value::factory()->make());

        $response = $this->json('GET', 'api/values/'.$value->id)
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
        $team = Team::factory()->create();

        /** @var Project $project */
        $project = $team->projects()->save(Project::factory()->make());

        /** @var Key $key */
        $key = $project->keys()->save(Key::factory()->make());

        /** @var Value $value */
        $value = $key->values()->save(Value::factory()->make());

        $response = $this->json('PATCH', 'api/values/'.$value->id)
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
        $team = Team::factory()->create();

        /** @var Project $project */
        $project = $team->projects()->save(Project::factory()->make());

        /** @var Key $key */
        $key = $project->keys()->save(Key::factory()->make());

        /** @var Value $value */
        $value = $key->values()->save(Value::factory()->make());

        $response = $this->json('DELETE', 'api/values/'.$value->id)
            ->assertForbidden();

        $this->assertEquals(
            ErrorType::PERMISSION_DENIED,
            $response->exception->getCode()
        );
    }
}
