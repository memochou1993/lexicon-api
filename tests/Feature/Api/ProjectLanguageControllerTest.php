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
        Sanctum::actingAs($this->user, [
            PermissionType::PROJECT_UPDATE,
        ]);

        /** @var Team $team */
        $team = factory(Team::class)->create();

        /** @var Project $project */
        $project = $team->projects()->save(factory(Project::class)->make());

        /** @var Language $language */
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
        Sanctum::actingAs($this->user, [
            PermissionType::PROJECT_UPDATE,
        ]);

        /** @var Team $team */
        $team = factory(Team::class)->create();

        /** @var Project $project */
        $project = $team->projects()->save(factory(Project::class)->make());

        /** @var Language $language */
        $language = $team->languages()->save(factory(Language::class)->make());
        $project->languages()->attach($language);

        /** @var Form $form */
        $form = $team->forms()->save(factory(Form::class)->make());
        $language->forms()->attach($form);

        /** @var Key $key */
        $key = $project->keys()->save(factory(Key::class)->make());

        /** @var Value $value */
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
        Sanctum::actingAs($this->user, [
            PermissionType::PROJECT_UPDATE,
        ]);

        $this->flushEventListeners(Project::class);

        /** @var Team $team */
        $team = factory(Team::class)->create();

        /** @var Project $project */
        $project = $team->projects()->save(factory(Project::class)->make());

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
        Sanctum::actingAs($this->user, [
            PermissionType::PROJECT_UPDATE,
        ]);

        $this->flushEventListeners(Project::class);

        /** @var Team $team */
        $team = factory(Team::class)->create();

        /** @var Project $project */
        $project = $team->projects()->save(factory(Project::class)->make());

        /** @var Language $language */
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
        Sanctum::actingAs($this->user);

        /** @var Team $team */
        $team = factory(Team::class)->create();

        /** @var Project $project */
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
        Sanctum::actingAs($this->user);

        /** @var Team $team */
        $team = factory(Team::class)->create();

        /** @var Project $project */
        $project = $team->projects()->save(factory(Project::class)->make());

        /** @var Language $language */
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
