<?php

namespace Tests\Feature\Api\Client;

use App\Models\Form;
use App\Models\Key;
use App\Models\Language;
use App\Models\Project;
use App\Models\Team;
use App\Models\Value;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Cache;
use Laravel\Sanctum\Sanctum;
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
        $team = factory(Team::class)->create();

        /** @var Language $language */
        $language = $team->languages()->save(factory(Language::class)->make());

        /** @var Form $form */
        $form = $team->forms()->save(factory(Form::class)->make());
        $language->forms()->attach($form);

        /** @var Project $project */
        $project = $team->projects()->save(factory(Project::class)->make());

        Sanctum::actingAs($project);

        /** @var Key $key */
        $key = $project->keys()->save(factory(Key::class)->make());

        /** @var Value $value */
        $key->values()->save(factory(Value::class)->make());

        /** @var Value $value */
        $value = $key->values()->save(factory(Value::class)->make());
        $value->languages()->attach($language);
        $value->forms()->attach($form);

        $this->json('GET', 'api/client/project')
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

        $this->assertTrue(Cache::has($project->cacheKey()));
    }
}
