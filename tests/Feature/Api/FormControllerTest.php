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
        $team = factory(Team::class)->create();

        $data = factory(Form::class)->make()->toArray();

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
        $team = factory(Team::class)->create();
        $team->forms()->save(factory(Form::class)->make([
            'name' => 'Unique Form',
        ]));

        $data = factory(Form::class)->make([
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
        $team = factory(Team::class)->create();

        /** @var Form $form */
        $form = $team->forms()->save(factory(Form::class)->make());

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
        $team = factory(Team::class)->create();

        /** @var Form $form */
        $form = $team->forms()->save(factory(Form::class)->make());

        $data = factory(Form::class)->make([
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
        $team = factory(Team::class)->create();

        /** @var Collection $forms */
        $forms = $team->forms()->saveMany(factory(Form::class, 2)->make());

        $data = factory(Form::class)->make([
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
        $team = factory(Team::class)->create();

        /** @var Language $language */
        $language = $team->languages()->save(factory(Language::class)->make());

        /** @var Form $form */
        $form = $team->forms()->save(factory(Form::class)->make());
        $language->forms()->attach($form);

        /** @var Project $project */
        $project = $team->projects()->save(factory(Project::class)->make());
        $project->languages()->attach($language);

        /** @var Key $key */
        $key = $project->keys()->save(factory(Key::class)->make());

        /** @var Value $value */
        $value = $key->values()->save(factory(Value::class)->make());
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
    public function testGuestCreate()
    {
        Sanctum::actingAs($this->user, [
            PermissionType::TEAM_VIEW,
            PermissionType::FORM_CREATE,
        ]);

        $this->flushEventListeners(Team::class);

        /** @var Team $team */
        $team = factory(Team::class)->create();

        $data = factory(Form::class)->make()->toArray();

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
    public function testGuestView()
    {
        Sanctum::actingAs($this->user, [
            PermissionType::FORM_VIEW,
        ]);

        $this->flushEventListeners(Team::class);

        /** @var Team $team */
        $team = factory(Team::class)->create();

        /** @var Form $form */
        $form = $team->forms()->save(factory(Form::class)->make());

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
    public function testGuestUpdate()
    {
        Sanctum::actingAs($this->user, [
            PermissionType::FORM_UPDATE,
        ]);

        $this->flushEventListeners(Team::class);

        /** @var Team $team */
        $team = factory(Team::class)->create();

        /** @var Form $form */
        $form = $team->forms()->save(factory(Form::class)->make());

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
    public function testGuestDelete()
    {
        Sanctum::actingAs($this->user, [
            PermissionType::FORM_DELETE,
        ]);

        $this->flushEventListeners(Team::class);

        /** @var Team $team */
        $team = factory(Team::class)->create();

        /** @var Form $form */
        $form = $team->forms()->save(factory(Form::class)->make());

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
        $team = factory(Team::class)->create();

        $data = factory(Form::class)->make()->toArray();

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
        $team = factory(Team::class)->create();

        /** @var Form $form */
        $form = $team->forms()->save(factory(Form::class)->make());

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
        $team = factory(Team::class)->create();

        /** @var Form $form */
        $form = $team->forms()->save(factory(Form::class)->make());

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
        $team = factory(Team::class)->create();

        /** @var Form $form */
        $form = $team->forms()->save(factory(Form::class)->make());

        $response = $this->json('DELETE', 'api/forms/'.$form->id)
            ->assertForbidden();

        $this->assertEquals(
            ErrorType::PERMISSION_DENIED,
            $response->exception->getCode()
        );
    }
}
