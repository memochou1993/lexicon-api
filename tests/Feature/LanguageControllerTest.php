<?php

namespace Tests\Feature;

use App\Enums\ErrorType;
use App\Enums\PermissionType;
use App\Models\Form;
use App\Models\Language;
use App\Models\Permission;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LanguageControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function testStore()
    {
        $user = Sanctum::actingAs($this->user, [
            PermissionType::TEAM_VIEW,
            PermissionType::LANGUAGE_CREATE,
        ]);

        $team = $user->teams()->save(factory(Team::class)->make());
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

        $this->assertCount(1, $team->languages);

        $this->assertCount(
            count($form_ids),
            Language::find(json_decode($response->getContent())->data->id)->forms
        );
    }

    /**
     * @return void
     */
    public function testStoreDuplicate()
    {
        $user = Sanctum::actingAs($this->user, [
            PermissionType::TEAM_VIEW,
            PermissionType::LANGUAGE_CREATE,
        ]);

        $team = $user->teams()->save(factory(Team::class)->make());
        $team->languages()->save(factory(Language::class)->make([
            'name' => 'Unique Language',
        ]));

        $data = factory(Language::class)->make([
            'name' => 'Unique Language',
        ])->toArray();

        $this->json('POST', 'api/teams/'.$team->id.'/languages', $data)
            ->assertJsonValidationErrors([
                'name',
            ]);

        $this->assertCount(1, $team->languages);
    }

    /**
     * @return void
     */
    public function testShow()
    {
        $user = Sanctum::actingAs($this->user, [PermissionType::LANGUAGE_VIEW]);

        $team = $user->teams()->save(factory(Team::class)->make());
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
        $user = Sanctum::actingAs($this->user, [PermissionType::LANGUAGE_UPDATE]);

        $form_ids = factory(Form::class, 2)->create()->pluck('id')->toArray();

        $team = $user->teams()->save(factory(Team::class)->make());
        $language = $team->languages()->save(factory(Language::class)->make());

        $data = factory(Language::class)->make([
            'name' => 'New Language',
            'form_ids' => $form_ids,
        ]);

        $this->json('PATCH', 'api/languages/'.$language->id, $data->toArray())
            ->assertOk()
            ->assertJson([
                'data' => $data->makeHidden('form_ids')->toArray(),
            ]);

        $this->assertDatabaseHas('languages', $data->toArray());

        $this->assertCount(count($form_ids), $language->forms);
    }

    /**
     * @return void
     */
    public function testUpdateDuplicate()
    {
        $user = Sanctum::actingAs($this->user, [PermissionType::LANGUAGE_UPDATE]);

        $team = $user->teams()->save(factory(Team::class)->make());
        $languages = $team->languages()->saveMany(factory(Language::class, 2)->make());

        $data = factory(Language::class)->make([
            'name' => $languages->last()->name,
        ])->toArray();

        $this->json('PATCH', 'api/languages/'.$languages->first()->id, $data)
            ->assertJsonValidationErrors([
                'name',
            ]);
    }

    /**
     * @return void
     */
    public function testDestroy()
    {
        $user = Sanctum::actingAs($this->user, [PermissionType::LANGUAGE_DELETE]);

        $team = $user->teams()->save(factory(Team::class)->make());
        $language = $team->languages()->save(factory(Language::class)->make());
        $form = $language->forms()->save(factory(Form::class)->make());

        $this->json('DELETE', 'api/languages/'.$language->id)
            ->assertNoContent();

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
    public function testGuestCreate()
    {
        Sanctum::actingAs($this->user, [
            PermissionType::TEAM_VIEW,
            PermissionType::LANGUAGE_CREATE,
        ]);

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
        Sanctum::actingAs($this->user, [PermissionType::LANGUAGE_VIEW]);

        $team = factory(Team::class)->create();
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
        Sanctum::actingAs($this->user, [PermissionType::LANGUAGE_UPDATE]);

        $team = factory(Team::class)->create();
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
        Sanctum::actingAs($this->user, [PermissionType::LANGUAGE_DELETE]);

        $team = factory(Team::class)->create();
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
        $user = Sanctum::actingAs($this->user);

        $team = $user->teams()->save(factory(Team::class)->make());

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
        $user = Sanctum::actingAs($this->user);

        $team = $user->teams()->save(factory(Team::class)->make());
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
        $user = Sanctum::actingAs($this->user);

        $team = $user->teams()->save(factory(Team::class)->make());
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
        $user = Sanctum::actingAs($this->user);

        $team = $user->teams()->save(factory(Team::class)->make());
        $language = $team->languages()->save(factory(Language::class)->make());

        $response = $this->json('DELETE', 'api/languages/'.$language->id)
            ->assertForbidden();

        $this->assertEquals(
            ErrorType::PERMISSION_DENIED,
            $response->exception->getCode()
        );
    }
}
