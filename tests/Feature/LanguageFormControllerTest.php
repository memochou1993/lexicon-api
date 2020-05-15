<?php

namespace Tests\Feature;

use App\Models\Form;
use App\Models\Language;
use App\Models\Project;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class LanguageFormControllerTest extends TestCase
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
    public function testStore()
    {
        $team = $this->user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->make());
        $language = $project->languages()->save(factory(Language::class)->make());
        $form = factory(Form::class)->create();

        $this->assertCount(0, $language->forms);

        $this->json('POST', 'api/languages/1/forms', [
            'form_ids' => $form->id,
        ])
            ->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertCount(1, $language->refresh()->forms);
    }

    /**
     * @return void
     */
    public function testStoreSync()
    {
        $team = $this->user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->make());
        $language = $project->languages()->save(factory(Language::class)->make());
        $form = $language->forms()->saveMany(factory(Form::class, 2)->make());

        $this->assertCount(2, $language->forms);

        $this->json('POST', 'api/languages/1/forms', [
            'form_ids' => $form->pluck('id')->first(),
            'sync' => true,
        ])
            ->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertCount(1, $language->refresh()->forms);
    }

    /**
     * @return void
     */
    public function testDestroy()
    {
        $team = $this->user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->make());
        $language = $project->languages()->save(factory(Language::class)->make());
        $language->forms()->save(factory(Form::class)->make());

        $this->assertCount(1, $language->forms);

        $this->json('DELETE', 'api/languages/1/forms/1')
            ->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertCount(0, $language->refresh()->forms);
    }
}
