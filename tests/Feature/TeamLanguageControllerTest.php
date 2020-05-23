<?php

namespace Tests\Feature;

use App\Enums\ErrorType;
use App\Enums\PermissionType;
use App\Models\Form;
use App\Models\Language;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TeamLanguageControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function testStore()
    {
        $user = Sanctum::actingAs($this->user, [
            PermissionType::TEAM_VIEW,
            PermissionType::LANGUAGE_CREATE,
        ]);

        $team = $user->teams()->save(factory(Team::class)->make());
        $form_ids = factory(Form::class, 2)->create()->pluck('id')->toArray();

        $data = factory(Language::class)->make([
            'form_ids' => $form_ids,
        ]);

        $response = $this->json('POST', 'api/teams/'.$team->id.'/languages', $data->toArray())
            ->assertCreated()
            ->assertJson([
                'data' => $data->makeHidden('form_ids')->toArray(),
            ]);

        $this->assertDatabaseHas('languages', $data->toArray());

        $this->assertCount(1, $team->languages);

        $this->assertCount(
            count($form_ids),
            Language::find(json_decode($response->getContent())->data->id)->forms
        );
    }

    /**
     * @return void
     */
    public function testStoreDuplicate()
    {
        $user = Sanctum::actingAs($this->user, [
            PermissionType::TEAM_VIEW,
            PermissionType::LANGUAGE_CREATE,
        ]);

        $team = $user->teams()->save(factory(Team::class)->make());
        $team->languages()->save(factory(Language::class)->make([
            'name' => 'Unique Language',
        ]));

        $data = factory(Language::class)->make([
            'name' => 'Unique Language',
        ])->toArray();

        $this->json('POST', 'api/teams/'.$team->id.'/languages', $data)
            ->assertJsonValidationErrors([
                'name',
            ]);

        $this->assertCount(1, $team->languages);
    }

    /**
     * @return void
     */
    public function testGuestCreate()
    {
        Sanctum::actingAs($this->user, [
            PermissionType::TEAM_VIEW,
            PermissionType::LANGUAGE_CREATE,
        ]);

        $team = factory(Team::class)->create();

        $data = factory(Language::class)->make()->toArray();

        $response = $this->json('POST', 'api/teams/'.$team->id.'/languages', $data)
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
        $user = Sanctum::actingAs($this->user);

        $team = $user->teams()->save(factory(Team::class)->make());

        $data = factory(Language::class)->make()->toArray();

        $response = $this->json('POST', 'api/teams/'.$team->id.'/languages', $data)
            ->assertForbidden();

        $this->assertEquals(
            ErrorType::PERMISSION_DENIED,
            $response->exception->getCode()
        );
    }
}
