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
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class FormControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function testStore()
    {
        Sanctum::actingAs($this->user, [
            PermissionType::TEAM_VIEW,
            PermissionType::FORM_CREATE,
        ]);

        /** @var Team $team */
        $team = Team::factory()->create();

        $data = Form::factory()->make()->toArray();

        $this->json('POST', 'api/teams/'.$team->id.'/forms', $data)
            ->assertCreated();

        $this->assertCount(1, $team->refresh()->forms);
    }

    /**
     * @return void
     */
    public function testStoreDuplicate()
    {
        Sanctum::actingAs($this->user, [
            PermissionType::TEAM_VIEW,
            PermissionType::FORM_CREATE,
        ]);

        /** @var Team $team */
        $team = Team::factory()->create();
        $team->forms()->save(Form::factory()->make([
            'name' => 'Unique Form',
        ]));

        $data = Form::factory()->make([
            'name' => 'Unique Form',
        ])->toArray();

        $this->json('POST', 'api/teams/'.$team->id.'/forms', $data)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors([
                'name',
            ]);

        $this->assertCount(1, $team->refresh()->forms);
    }

    /**
     * @return void
     */
    public function testShow()
    {
        Sanctum::actingAs($this->user, [
            PermissionType::FORM_VIEW,
        ]);

        /** @var Team $team */
        $team = Team::factory()->create();

        /** @var Form $form */
        $form = $team->forms()->save(Form::factory()->make());

        $this->json('GET', 'api/forms/'.$form->id, [
            'relations' => '',
        ])
            ->assertOk()
            ->assertJsonStructure([
                'data',
            ])
            ->assertJson([
                'data' => $form->toArray(),
            ]);
    }

    /**
     * @return void
     */
    public function testUpdate()
    {
        Sanctum::actingAs($this->user, [
            PermissionType::FORM_UPDATE,
        ]);

        /** @var Team $team */
        $team = Team::factory()->create();

        /** @var Form $form */
        $form = $team->forms()->save(Form::factory()->make());

        $data = Form::factory()->make([
            'name' => 'New Form',
        ])->toArray();

        $this->json('PATCH', 'api/forms/'.$form->id, $data)
            ->assertOk();
    }

    /**
     * @return void
     */
    public function testUpdateDuplicate()
    {
        Sanctum::actingAs($this->user, [
            PermissionType::FORM_UPDATE,
        ]);

        /** @var Team $team */
        $team = Team::factory()->create();

        /** @var Collection $forms */
        $forms = $team->forms()->saveMany(Form::factory()->count(2)->make());

        $data = Form::factory()->make([
            'name' => $forms->last()->name,
        ])->toArray();

        $this->json('PATCH', 'api/forms/'.$forms->first()->id, $data)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors([
                'name',
            ]);
    }

    /**
     * @return void
     */
    public function testDestroy()
    {
        Sanctum::actingAs($this->user, [
            PermissionType::FORM_DELETE,
        ]);

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
        $value = $key->values()->save(Value::factory()->make());
        $value->languages()->attach($language);
        $value->forms()->attach($form);

        $this->json('DELETE', 'api/forms/'.$form->id)
            ->assertNoContent();

        $this->assertDeleted($form);

        $this->assertDeleted($value);
    }

    /**
     * @return void
     */
    public function testCreateByGuest()
    {
        Sanctum::actingAs($this->user, [
            PermissionType::TEAM_VIEW,
            PermissionType::FORM_CREATE,
        ]);

        $this->flushEventListeners(Team::class);

        /** @var Team $team */
        $team = Team::factory()->create();

        $data = Form::factory()->make()->toArray();

        $response = $this->json('POST', 'api/teams/'.$team->id.'/forms', $data)
            ->assertForbidden();

        $this->assertEquals(
            ErrorType::USER_NOT_IN_TEAM,
            $response->exception->getCode()
        );
    }

    /**
     * @return void
     */
    public function testViewByGuest()
    {
        Sanctum::actingAs($this->user, [
            PermissionType::FORM_VIEW,
        ]);

        $this->flushEventListeners(Team::class);

        /** @var Team $team */
        $team = Team::factory()->create();

        /** @var Form $form */
        $form = $team->forms()->save(Form::factory()->make());

        $response = $this->json('GET', 'api/forms/'.$form->id)
            ->assertForbidden();

        $this->assertEquals(
            ErrorType::USER_NOT_IN_TEAM,
            $response->exception->getCode()
        );
    }

    /**
     * @return void
     */
    public function testUpdateByGuest()
    {
        Sanctum::actingAs($this->user, [
            PermissionType::FORM_UPDATE,
        ]);

        $this->flushEventListeners(Team::class);

        /** @var Team $team */
        $team = Team::factory()->create();

        /** @var Form $form */
        $form = $team->forms()->save(Form::factory()->make());

        $response = $this->json('PATCH', 'api/forms/'.$form->id)
            ->assertForbidden();

        $this->assertEquals(
            ErrorType::USER_NOT_IN_TEAM,
            $response->exception->getCode()
        );
    }

    /**
     * @return void
     */
    public function testDeleteByGuest()
    {
        Sanctum::actingAs($this->user, [
            PermissionType::FORM_DELETE,
        ]);

        $this->flushEventListeners(Team::class);

        /** @var Team $team */
        $team = Team::factory()->create();

        /** @var Form $form */
        $form = $team->forms()->save(Form::factory()->make());

        $response = $this->json('DELETE', 'api/forms/'.$form->id)
            ->assertForbidden();

        $this->assertEquals(
            ErrorType::USER_NOT_IN_TEAM,
            $response->exception->getCode()
        );
    }

    /**
     * @return void
     */
    public function testCreateWithoutPermission()
    {
        Sanctum::actingAs($this->user);

        /** @var Team $team */
        $team = Team::factory()->create();

        $data = Form::factory()->make()->toArray();

        $response = $this->json('POST', 'api/teams/'.$team->id.'/forms', $data)
            ->assertForbidden();

        $this->assertEquals(
            ErrorType::PERMISSION_DENIED,
            $response->exception->getCode()
        );
    }

    /**
     * @return void
     */
    public function testViewWithoutPermission()
    {
        Sanctum::actingAs($this->user);

        /** @var Team $team */
        $team = Team::factory()->create();

        /** @var Form $form */
        $form = $team->forms()->save(Form::factory()->make());

        $response = $this->json('GET', 'api/forms/'.$form->id)
            ->assertForbidden();

        $this->assertEquals(
            ErrorType::PERMISSION_DENIED,
            $response->exception->getCode()
        );
    }

    /**
     * @return void
     */
    public function testUpdateWithoutPermission()
    {
        Sanctum::actingAs($this->user);

        /** @var Team $team */
        $team = Team::factory()->create();

        /** @var Form $form */
        $form = $team->forms()->save(Form::factory()->make());

        $response = $this->json('PATCH', 'api/forms/'.$form->id)
            ->assertForbidden();

        $this->assertEquals(
            ErrorType::PERMISSION_DENIED,
            $response->exception->getCode()
        );
    }

    /**
     * @return void
     */
    public function testDeleteWithoutPermission()
    {
        Sanctum::actingAs($this->user);

        /** @var Team $team */
        $team = Team::factory()->create();

        /** @var Form $form */
        $form = $team->forms()->save(Form::factory()->make());

        $response = $this->json('DELETE', 'api/forms/'.$form->id)
            ->assertForbidden();

        $this->assertEquals(
            ErrorType::PERMISSION_DENIED,
            $response->exception->getCode()
        );
    }
}
