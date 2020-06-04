<?php

namespace Tests\Feature\Api;

use App\Enums\ErrorType;
use App\Enums\PermissionType;
use App\Models\Form;
use App\Models\Language;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class TeamControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function testIndex()
    {
        Sanctum::actingAs($this->user, [
            PermissionType::TEAM_VIEW_ANY,
        ]);

        /** @var Team $team */
        $team = factory(Team::class)->create();

        $this->json('GET', 'api/teams', [
            'relations' => 'users,projects,languages,forms',
        ])
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    [
                        'users',
                        'projects',
                        'languages',
                        'forms',
                    ],
                ],
            ])
            ->assertJson([
                'data' => [
                    $team->toArray(),
                ],
            ]);
    }

    /**
     * @return void
     */
    public function testShow()
    {
        Sanctum::actingAs($this->user, [
            PermissionType::TEAM_VIEW,
        ]);

        /** @var Team $team */
        $team = factory(Team::class)->create();

        $this->json('GET', 'api/teams/'.$team->id, [
            'relations' => 'users,projects,languages,forms',
        ])
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'users',
                    'projects',
                    'languages',
                    'forms',
                ],
            ])
            ->assertJson([
                'data' => $team->toArray(),
            ]);
    }

    /**
     * @return void
     */
    public function testUpdate()
    {
        Sanctum::actingAs($this->user, [
            PermissionType::TEAM_UPDATE,
        ]);

        /** @var Team $team */
        $team = factory(Team::class)->create();

        $data = factory(Team::class)->make([
            'name' => 'New Team',
        ])->toArray();

        $this->json('PATCH', 'api/teams/'.$team->id, $data)
            ->assertOk()
            ->assertJson([
                'data' => $data,
            ]);

        $this->assertDatabaseHas('teams', $data);
    }

    /**
     * @return void
     */
    public function testUpdateDuplicate()
    {
        Sanctum::actingAs($this->user, [
            PermissionType::TEAM_UPDATE,
        ]);

        /** @var Collection $team */
        $teams = factory(Team::class, 2)->create();

        $data = factory(Team::class)->make([
            'name' => $teams->last()->name,
        ])->toArray();

        $this->json('PATCH', 'api/teams/'.$teams->first()->id, $data)
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
        /** @var User $user */
        $user = Sanctum::actingAs($this->user, [
            PermissionType::TEAM_DELETE,
        ]);

        /** @var Team $team */
        $team = factory(Team::class)->create();

        /** @var Language $language */
        $language = $team->languages()->save(factory(Language::class)->make());

        /** @var Form $form */
        $form = $team->forms()->save(factory(Form::class)->make());

        $this->assertCount(1, $team->users);
        $this->assertCount(1, $team->languages);
        $this->assertCount(1, $team->forms);

        $this->json('DELETE', 'api/teams/'.$team->id)
            ->assertNoContent();

        $this->assertDeleted($team);

        $this->assertDatabaseMissing('model_has_users', [
            'user_id' => $user->id,
            'model_type' => 'team',
            'model_id' => $team->id,
        ]);

        $this->assertDatabaseMissing('model_has_languages', [
            'language_id' => $language->id,
            'model_type' => 'team',
            'model_id' => $team->id,
        ]);

        $this->assertDatabaseMissing('model_has_forms', [
            'form_id' => $form->id,
            'model_type' => 'team',
            'model_id' => $team->id,
        ]);
    }

    /**
     * @return void
     */
    public function testGuestView()
    {
        Sanctum::actingAs($this->user, [
            PermissionType::TEAM_VIEW,
        ]);

        $this->flushEventListeners(Team::class);

        /** @var Team $team */
        $team = factory(Team::class)->create();

        $response = $this->json('GET', 'api/teams/'.$team->id)
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
            PermissionType::TEAM_UPDATE,
        ]);

        $this->flushEventListeners(Team::class);

        /** @var Team $team */
        $team = factory(Team::class)->create();

        $response = $this->json('PATCH', 'api/teams/'.$team->id)
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
            PermissionType::TEAM_DELETE,
        ]);

        $this->flushEventListeners(Team::class);

        /** @var Team $team */
        $team = factory(Team::class)->create();

        $response = $this->json('DELETE', 'api/teams/'.$team->id)
            ->assertForbidden();

        $this->assertEquals(
            ErrorType::USER_NOT_IN_TEAM,
            $response->exception->getCode()
        );
    }

    /**
     * @return void
     */
    public function testViewAllWithoutPermission()
    {
        Sanctum::actingAs($this->user);

        $response = $this->json('GET', 'api/teams')
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

        $response = $this->json('GET', 'api/teams/'.$team->id)
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

        $response = $this->json('PATCH', 'api/teams/'.$team->id)
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

        $response = $this->json('DELETE', 'api/teams/'.$team->id)
            ->assertForbidden();

        $this->assertEquals(
            ErrorType::PERMISSION_DENIED,
            $response->exception->getCode()
        );
    }
}
