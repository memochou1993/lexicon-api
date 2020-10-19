<?php

namespace Tests\Feature\Api\Project;

use App\Models\Form;
use App\Models\Key;
use App\Models\Language;
use App\Models\Project;
use App\Models\Team;
use App\Models\Value;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function testShow()
    {
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
        $key->values()->save(Value::factory()->make());

        /** @var Value $value */
        $value = $key->values()->save(Value::factory()->make());
        $value->languages()->attach($language);
        $value->forms()->attach($form);

        $this->withToken($project->getSetting('api_key'))
            ->json('GET', 'api/project')
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'languages',
                    'keys' => [
                        [
                            'values' => [
                                [
                                    'language',
                                    'form',
                                ],
                            ],
                        ],
                    ],
                ],
            ]);
    }

    /**
     * @return void
     */
    public function testUnauthorized()
    {
        $this->json('GET', 'api/project')
            ->assertUnauthorized();
    }
}
