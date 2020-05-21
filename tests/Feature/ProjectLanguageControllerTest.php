<?php

namespace Tests\Feature;

use App\Models\Language;
use App\Models\Project;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProjectLanguageControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function testAttach()
    {
        $user = Sanctum::actingAs($this->user, ['update-project']);

        $team = $user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->make());
        $language = factory(Language::class)->create();

        $this->assertCount(0, $project->languages);

        $this->json('POST', 'api/projects/'.$project->id.'/languages', [
            'language_ids' => $language->id,
        ])
            ->assertNoContent();

        $this->assertCount(1, $project->refresh()->languages);
    }

    /**
     * @return void
     */
    public function testSync()
    {
        $user = Sanctum::actingAs($this->user, ['update-project']);

        $team = $user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->make());
        $language = $project->languages()->saveMany(factory(Language::class, 2)->make());

        $this->assertCount(2, $project->languages);

        $this->json('POST', 'api/projects/'.$project->id.'/languages', [
            'language_ids' => $language->pluck('id')->first(),
            'sync' => true,
        ])
            ->assertNoContent();

        $this->assertCount(1, $project->refresh()->languages);
    }

    /**
     * @return void
     */
    public function testDetach()
    {
        $user = Sanctum::actingAs($this->user, ['update-project']);

        $team = $user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->make());
        $language = $project->languages()->save(factory(Language::class)->make());

        $this->assertCount(1, $project->languages);

        $this->json('DELETE', 'api/projects/'.$project->id.'/languages/'.$language->id)
            ->assertNoContent();

        $this->assertCount(0, $project->refresh()->languages);
    }

    /**
     * @return void
     */
    public function testAttachForbidden()
    {
        $user = Sanctum::actingAs($this->user, ['update-project']);

        $team = $user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->withoutEvents()->make());

        $this->json('POST', 'api/projects/'.$project->id.'/languages')
            ->assertForbidden();
    }

    /**
     * @return void
     */
    public function testDetachForbidden()
    {
        $user = Sanctum::actingAs($this->user, ['update-project']);

        $team = $user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->withoutEvents()->make());
        $language = $project->languages()->save(factory(Language::class)->make());

        $this->json('DELETE', 'api/projects/'.$project->id.'/languages/'.$language->id)
            ->assertForbidden();
    }

    // TODO: make WithoutPermission() tests
}
