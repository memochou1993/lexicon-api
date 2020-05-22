<?php

namespace Tests\Feature;

use App\Enums\PermissionType;
use App\Models\Form;
use App\Models\Language;
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

        $team = $user->teams()->save(factory(Team::class)->make());
        $language = $team->languages()->save(factory(Language::class)->make());

        $data = factory(Language::class)->make([
            'name' => 'New Language',
        ])->toArray();

        $this->json('PATCH', 'api/languages/'.$language->id, $data)
            ->assertOk()
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
    public function testGuestView()
    {
        Sanctum::actingAs($this->user, [PermissionType::LANGUAGE_VIEW]);

        $team = factory(Team::class)->create();
        $language = $team->languages()->save(factory(Language::class)->make());

        $this->json('GET', 'api/languages/'.$language->id)
            ->assertForbidden();
    }

    /**
     * @return void
     */
    public function testGuestUpdate()
    {
        Sanctum::actingAs($this->user, [PermissionType::LANGUAGE_UPDATE]);

        $team = factory(Team::class)->create();
        $language = $team->languages()->save(factory(Language::class)->make());

        $this->json('PATCH', 'api/languages/'.$language->id)
            ->assertForbidden();
    }

    /**
     * @return void
     */
    public function testGuestDelete()
    {
        Sanctum::actingAs($this->user, [PermissionType::LANGUAGE_DELETE]);

        $team = factory(Team::class)->create();
        $language = $team->languages()->save(factory(Language::class)->make());

        $this->json('DELETE', 'api/languages/'.$language->id)
            ->assertForbidden();
    }

    /**
     * @return void
     */
    public function testViewWithoutPermission()
    {
        $user = Sanctum::actingAs($this->user);

        $team = $user->teams()->save(factory(Team::class)->make());
        $language = $team->languages()->save(factory(Language::class)->make());

        $this->json('GET', 'api/languages/'.$language->id)
            ->assertForbidden();
    }

    /**
     * @return void
     */
    public function testUpdateWithoutPermission()
    {
        $user = Sanctum::actingAs($this->user);

        $team = $user->teams()->save(factory(Team::class)->make());
        $language = $team->languages()->save(factory(Language::class)->make());

        $this->json('PATCH', 'api/languages/'.$language->id)
            ->assertForbidden();
    }

    /**
     * @return void
     */
    public function testDeleteWithoutPermission()
    {
        $user = Sanctum::actingAs($this->user);

        $team = $user->teams()->save(factory(Team::class)->make());
        $language = $team->languages()->save(factory(Language::class)->make());

        $this->json('DELETE', 'api/languages/'.$language->id)
            ->assertForbidden();
    }
}
