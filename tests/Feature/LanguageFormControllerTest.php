<?php

namespace Tests\Feature;

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
        $user = Sanctum::actingAs($this->user, ['update-language']);

        $team = $user->teams()->save(factory(Team::class)->make());
        $team->projects()->save(factory(Project::class)->make());
        $language = $team->languages()->save(factory(Language::class)->make());
        $form = factory(Form::class)->create();

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
        $user = Sanctum::actingAs($this->user, ['update-language']);

        $team = $user->teams()->save(factory(Team::class)->make());
        $team->projects()->save(factory(Project::class)->make());
        $language = $team->languages()->save(factory(Language::class)->make());
        $form = $language->forms()->saveMany(factory(Form::class, 2)->make());

        $this->assertCount(2, $language->forms);

        $this->json('POST', 'api/languages/'.$language->id.'/forms', [
            'form_ids' => $form->pluck('id')->first(),
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
        $user = Sanctum::actingAs($this->user, ['update-language']);

        $team = $user->teams()->save(factory(Team::class)->make());
        $team->projects()->save(factory(Project::class)->make());
        $language = $team->languages()->save(factory(Language::class)->make());
        $form = $language->forms()->save(factory(Form::class)->make());

        $this->assertCount(1, $language->forms);

        $this->json('DELETE', 'api/languages/'.$language->id.'/forms/'.$form->id)
            ->assertNoContent();

        $this->assertCount(0, $language->refresh()->forms);
    }

    /**
     * @return void
     */
    public function testAttachForbidden()
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
    public function testDetachForbidden()
    {
        $user = Sanctum::actingAs($this->user);

        $team = $user->teams()->save(factory(Team::class)->make());
        $team->projects()->save(factory(Project::class)->make());
        $language = $team->languages()->save(factory(Language::class)->make());
        $form = $language->forms()->save(factory(Form::class)->make());

        $this->json('DELETE', 'api/languages/'.$language->id.'/forms/'.$form->id)
            ->assertForbidden();
    }
}
