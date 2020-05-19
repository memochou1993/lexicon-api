<?php

namespace Tests\Feature;

use App\Models\Language;
use App\Models\Project;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ProjectLanguageControllerTest extends TestCase
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
    public function testAttach()
    {
        $team = $this->user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->make());
        $language = factory(Language::class)->create();

        $this->assertCount(0, $project->languages);

        $this->json('POST', 'api/projects/'.$project->id.'/languages', [
            'language_ids' => $language->id,
        ])
            ->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertCount(1, $project->refresh()->languages);
    }

    /**
     * @return void
     */
    public function testAttachForbidden()
    {
        $user = factory(User::class)->create();
        $team = $user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->withoutEvents()->make());
        $language = factory(Language::class)->create();

        $this->json('POST', 'api/projects/'.$project->id.'/languages', [
            'language_ids' => $language->id,
        ])
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * @return void
     */
    public function testSync()
    {
        $team = $this->user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->make());
        $language = $project->languages()->saveMany(factory(Language::class, 2)->make());

        $this->assertCount(2, $project->languages);

        $this->json('POST', 'api/projects/'.$project->id.'/languages', [
            'language_ids' => $language->pluck('id')->first(),
            'sync' => true,
        ])
            ->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertCount(1, $project->refresh()->languages);
    }

    /**
     * @return void
     */
    public function testDetach()
    {
        $team = $this->user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->make());
        $language = $project->languages()->save(factory(Language::class)->make());

        $this->assertCount(1, $project->languages);

        $this->json('DELETE', 'api/projects/'.$project->id.'/languages/'.$language->id)
            ->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertCount(0, $project->refresh()->languages);
    }

    /**
     * @return void
     */
    public function testDetachForbidden()
    {
        $user = factory(User::class)->create();
        $team = $user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->withoutEvents()->make());
        $language = $project->languages()->save(factory(Language::class)->make());

        $this->json('DELETE', 'api/projects/'.$project->id.'/languages/'.$language->id)
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }
}
