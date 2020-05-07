<?php

namespace Tests\Feature;

use App\Models\Language;
use App\Models\Project;
use App\Models\Team;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LanguageControllerTest extends TestCase
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
        $language = factory(Language::class)->make([
            'project_id' => 1,
        ]);

        $this->json('POST', 'api/languages', $language->toArray())
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJson([
                'data' => $language->makeHidden('project_id')->toArray(),
            ]);

        $this->assertDatabaseHas('languages', $language->toArray());

        $this->assertCount(1, $project->languages);
    }

    /**
     * @return void
     */
    public function testUpdate()
    {
        factory(Language::class)->create();

        $language = factory(Language::class)->make([
            'name' => 'New Language',
        ])->toArray();

        $this->json('PATCH', 'api/languages/1', $language)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'data' => $language,
            ]);

        $this->assertDatabaseHas('languages', $language);
    }

    /**
     * @return void
     */
    public function testDestroy()
    {
        // TODO

        $team = $this->user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->make());
        $language = $project->languages()->save(factory(Language::class)->make());

        $this->json('DELETE', 'api/languages/1')
            ->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDeleted($language);

        $this->assertDatabaseMissing('model_has_languages', [
            'language_id' => $language->id,
            'model_type' => 'team',
            'model_id' => $team->id,
        ]);

        $this->assertDatabaseMissing('model_has_languages', [
            'language_id' => $language->id,
            'model_type' => 'project',
            'model_id' => $project->id,
        ]);
    }
}
