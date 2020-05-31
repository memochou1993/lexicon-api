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
use Illuminate\Foundation\Testing\WithFaker;
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
        $user = Sanctum::actingAs($this->user, [
            PermissionType::KEY_VIEW,
            PermissionType::VALUE_CREATE,
        ]);

        $team = $user->teams()->save(factory(Team::class)->make());
        $language = $team->languages()->save(factory(Language::class)->make());
        $form = $team->forms()->save(factory(Form::class)->make());
        $project = $team->projects()->save(factory(Project::class)->make());
        $key = $project->keys()->save(factory(Key::class)->make());

        $value = factory(Value::class)->make([
            'language_id' => $language->id,
            'form_id' => $form->id,
        ]);

        $this->json('POST', 'api/keys/'.$key->id.'/values', $value->toArray())
            ->assertCreated()
            ->assertJson([
                'data' => $value->makeHidden('language_id', 'form_id')->toArray(),
            ]);

        $this->assertDatabaseHas('values', $value->toArray());

        $this->assertCount(1, $key->refresh()->values);
    }

    /**
     * @return void
     */
    public function testShow()
    {
        $user = Sanctum::actingAs($this->user, [PermissionType::VALUE_VIEW]);

        $team = $user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->make());
        $key = $project->keys()->save(factory(Key::class)->make());
        $value = $key->values()->save(factory(Value::class)->make());

        $this->json('GET', 'api/values/'.$value->id)
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
        $user = Sanctum::actingAs($this->user, [PermissionType::VALUE_UPDATE]);

        $team = $user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->make());
        $key = $project->keys()->save(factory(Key::class)->make());
        $value = $key->values()->save(factory(Value::class)->make());

        $data = factory(Value::class)->make([
            'text' => 'New Value',
        ])->toArray();

        $this->json('PATCH', 'api/values/'.$value->id, $data)
            ->assertOk()
            ->assertJson([
                'data' => $data,
            ]);

        $this->assertDatabaseHas('values', $data);

        $this->assertCount(1, $key->refresh()->values);
    }

    /**
     * @return void
     */
    public function testDestroy()
    {
        $user = Sanctum::actingAs($this->user, [PermissionType::VALUE_DELETE]);

        $team = $user->teams()->save(factory(Team::class)->make());
        $language = $team->languages()->save(factory(Language::class)->make());
        $form = $team->forms()->save(factory(Form::class)->make());
        $language->forms()->attach($form);
        $project = $team->projects()->save(factory(Project::class)->make());
        $project->languages()->attach($language);
        $key = $project->keys()->save(factory(Key::class)->make());
        $value = $key->values()->save(factory(Value::class)->make());
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
    public function testGuestCreate()
    {
        $user = Sanctum::actingAs($this->user, [
            PermissionType::KEY_VIEW,
            PermissionType::VALUE_CREATE,
        ]);

        $team = $user->teams()->save(factory(Team::class)->make());
        $language = $team->languages()->save(factory(Language::class)->make());
        $form = $team->forms()->save(factory(Form::class)->make());
        $project = $team->projects()->save(factory(Project::class)->withoutEvents()->make());
        $key = $project->keys()->save(factory(Key::class)->make());

        $value = factory(Value::class)->make([
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
    public function testGuestView()
    {
        $user = Sanctum::actingAs($this->user, [PermissionType::VALUE_VIEW]);

        $team = $user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->withoutEvents()->make());
        $key = $project->keys()->save(factory(Key::class)->make());
        $value = $key->values()->save(factory(Value::class)->make());

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
    public function testGuestUpdate()
    {
        $user = Sanctum::actingAs($this->user, [PermissionType::VALUE_UPDATE]);

        $team = $user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->withoutEvents()->make());
        $key = $project->keys()->save(factory(Key::class)->make());
        $value = $key->values()->save(factory(Value::class)->make());

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
    public function testGuestDelete()
    {
        $user = Sanctum::actingAs($this->user, [PermissionType::VALUE_DELETE]);

        $team = $user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->withoutEvents()->make());
        $key = $project->keys()->save(factory(Key::class)->make());
        $value = $key->values()->save(factory(Value::class)->make());

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
        $user = Sanctum::actingAs($this->user);

        $team = $user->teams()->save(factory(Team::class)->make());
        $language = $team->languages()->save(factory(Language::class)->make());
        $form = $team->forms()->save(factory(Form::class)->make());
        $project = $team->projects()->save(factory(Project::class)->make());
        $key = $project->keys()->save(factory(Key::class)->make());

        $value = factory(Value::class)->make([
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
        $user = Sanctum::actingAs($this->user);

        $team = $user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->make());
        $key = $project->keys()->save(factory(Key::class)->make());
        $value = $key->values()->save(factory(Value::class)->make());

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
        $user = Sanctum::actingAs($this->user);

        $team = $user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->make());
        $key = $project->keys()->save(factory(Key::class)->make());
        $value = $key->values()->save(factory(Value::class)->make());

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
        $user = Sanctum::actingAs($this->user);

        $team = $user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->make());
        $key = $project->keys()->save(factory(Key::class)->make());
        $value = $key->values()->save(factory(Value::class)->make());

        $response = $this->json('DELETE', 'api/values/'.$value->id)
            ->assertForbidden();

        $this->assertEquals(
            ErrorType::PERMISSION_DENIED,
            $response->exception->getCode()
        );
    }
}
