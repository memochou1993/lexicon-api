<?php

namespace Tests\Feature\Api;

use App\Enums\ErrorType;
use App\Enums\PermissionType;
use App\Models\Form;
use App\Models\Key;
use App\Models\Language;
use App\Models\Project;
use App\Models\Team;
use App\Models\Value;
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
        $user = Sanctum::actingAs($this->user, [PermissionType::PROJECT_UPDATE]);

        $team = $user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->make());
        $language = $team->languages()->save(factory(Language::class)->make());

        $this->assertCount(0, $project->languages);

        $this->json('POST', 'api/projects/'.$project->id.'/languages', [
            'language_ids' => $language->id,
        ])
            ->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        $this->assertCount(1, $project->refresh()->languages);
    }

    /**
     * @return void
     */
    public function testDetach()
    {
        $user = Sanctum::actingAs($this->user, [PermissionType::PROJECT_UPDATE]);

        $team = $user->teams()->save(factory(Team::class)->make());
        $language = $team->languages()->save(factory(Language::class)->make());
        $form = $team->forms()->save(factory(Form::class)->make());
        $language->forms()->attach($form);
        $project = $team->projects()->save(factory(Project::class)->make());
        $project->languages()->attach($language);
        $key = $project->keys()->save(factory(Key::class)->make());
        $value = $key->values()->save(factory(Value::class)->make());
        $value->languages()->attach($language);
        $value->forms()->attach($form);

        $this->assertCount(1, $project->languages);

        $this->json('DELETE', 'api/projects/'.$project->id.'/languages/'.$language->id)
            ->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDeleted($value);

        $this->assertCount(0, $project->refresh()->languages);
        $this->assertCount(0, $project->refresh()->values);
    }

    /**
     * @return void
     */
    public function testGuestAttach()
    {
        $user = Sanctum::actingAs($this->user, [PermissionType::PROJECT_UPDATE]);

        $team = $user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->disableEvents()->make());

        $response = $this->json('POST', 'api/projects/'.$project->id.'/languages')
            ->assertForbidden();

        $this->assertEquals(
            ErrorType::USER_NOT_IN_PROJECT,
            $response->exception->getCode()
        );
    }

    /**
     * @return void
     */
    public function testGuestDetach()
    {
        $user = Sanctum::actingAs($this->user, [PermissionType::PROJECT_UPDATE]);

        $team = $user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->disableEvents()->make());
        $language = $team->languages()->save(factory(Language::class)->make());
        $project->languages()->attach($language);

        $response = $this->json('DELETE', 'api/projects/'.$project->id.'/languages/'.$language->id)
            ->assertForbidden();

        $this->assertEquals(
            ErrorType::USER_NOT_IN_PROJECT,
            $response->exception->getCode()
        );
    }

    /**
     * @return void
     */
    public function testAttachWithoutPermission()
    {
        $user = Sanctum::actingAs($this->user);

        $team = $user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->make());

        $response = $this->json('POST', 'api/projects/'.$project->id.'/languages')
            ->assertForbidden();

        $this->assertEquals(
            ErrorType::PERMISSION_DENIED,
            $response->exception->getCode()
        );
    }

    /**
     * @return void
     */
    public function testDetachWithoutPermission()
    {
        $user = Sanctum::actingAs($this->user);

        $team = $user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->make());
        $language = $team->languages()->save(factory(Language::class)->make());
        $project->languages()->attach($language);

        $response = $this->json('DELETE', 'api/projects/'.$project->id.'/languages/'.$language->id)
            ->assertForbidden();

        $this->assertEquals(
            ErrorType::PERMISSION_DENIED,
            $response->exception->getCode()
        );
    }
}
