<?php

namespace Tests\Feature;

use App\Enums\PermissionType;
use App\Models\Form;
use App\Models\Language;
use App\Models\Project;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LanguageFormControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function testAttach()
    {
        $user = Sanctum::actingAs($this->user, [PermissionType::LANGUAGE_UPDATE]);

        $team = $user->teams()->save(factory(Team::class)->make());
        $team->projects()->save(factory(Project::class)->make());
        $language = $team->languages()->save(factory(Language::class)->make());
        $form = $team->forms()->save(factory(Form::class)->make());

        $this->assertCount(0, $language->forms);

        $this->json('POST', 'api/languages/'.$language->id.'/forms', [
            'form_ids' => $form->id,
        ])
            ->assertNoContent();

        $this->assertCount(1, $language->refresh()->forms);
    }

    /**
     * @return void
     */
    public function testSync()
    {
        $user = Sanctum::actingAs($this->user, [PermissionType::LANGUAGE_UPDATE]);

        $team = $user->teams()->save(factory(Team::class)->make());
        $team->projects()->save(factory(Project::class)->make());
        $language = $team->languages()->save(factory(Language::class)->make());
        $forms = $team->forms()->saveMany(factory(Form::class, 2)->make());
        $language->forms()->attach($forms);

        $this->assertCount(2, $language->forms);

        $this->json('POST', 'api/languages/'.$language->id.'/forms', [
            'form_ids' => $forms->pluck('id')->first(),
            'sync' => true,
        ])
            ->assertNoContent();

        $this->assertCount(1, $language->refresh()->forms);
    }

    /**
     * @return void
     */
    public function testDetach()
    {
        $user = Sanctum::actingAs($this->user, [PermissionType::LANGUAGE_UPDATE]);

        $team = $user->teams()->save(factory(Team::class)->make());
        $team->projects()->save(factory(Project::class)->make());
        $language = $team->languages()->save(factory(Language::class)->make());
        $form = $team->forms()->save(factory(Form::class)->make());
        $language->forms()->attach($form);

        $this->assertCount(1, $language->forms);

        $this->json('DELETE', 'api/languages/'.$language->id.'/forms/'.$form->id)
            ->assertNoContent();

        $this->assertCount(0, $language->refresh()->forms);
    }

    /**
     * @return void
     */
    public function testGuestAttach()
    {
        Sanctum::actingAs($this->user, [PermissionType::LANGUAGE_UPDATE]);

        $team = factory(Team::class)->create();
        $team->projects()->save(factory(Project::class)->make());
        $language = $team->languages()->save(factory(Language::class)->make());

        $this->json('POST', 'api/languages/'.$language->id.'/forms')
            ->assertForbidden();
    }

    /**
     * @return void
     */
    public function testGuestDetach()
    {
        Sanctum::actingAs($this->user);

        $team = factory(Team::class)->create();
        $team->projects()->save(factory(Project::class)->make());
        $language = $team->languages()->save(factory(Language::class)->make());
        $form = $team->forms()->save(factory(Form::class)->make());
        $language->forms()->attach($form);

        $this->json('DELETE', 'api/languages/'.$language->id.'/forms/'.$form->id)
            ->assertForbidden();
    }

    /**
     * @return void
     */
    public function testAttachWithoutPermission()
    {
        $user = Sanctum::actingAs($this->user);

        $team = $user->teams()->save(factory(Team::class)->make());
        $team->projects()->save(factory(Project::class)->make());
        $language = $team->languages()->save(factory(Language::class)->make());

        $this->json('POST', 'api/languages/'.$language->id.'/forms')
            ->assertForbidden();
    }

    /**
     * @return void
     */
    public function testDetachWithoutPermission()
    {
        $user = Sanctum::actingAs($this->user);

        $team = $user->teams()->save(factory(Team::class)->make());
        $team->projects()->save(factory(Project::class)->make());
        $language = $team->languages()->save(factory(Language::class)->make());
        $form = $team->forms()->save(factory(Form::class)->make());
        $language->forms()->attach($form);

        $this->json('DELETE', 'api/languages/'.$language->id.'/forms/'.$form->id)
            ->assertForbidden();
    }
}
