<?php

namespace Tests\Feature\Api;

use App\Enums\ErrorType;
use App\Enums\PermissionType;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TeamUserControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function testAttach()
    {
        $user = Sanctum::actingAs($this->user, [PermissionType::TEAM_UPDATE]);

        $team = $user->teams()->save(factory(Team::class)->make());
        $member = factory(User::class)->create();

        $this->assertCount(1, $team->users);

        $this->json('POST', 'api/teams/'.$team->id.'/users', [
            'user_ids' => $member->id,
        ])
            ->assertNoContent();

        $this->assertCount(2, $team->refresh()->users);
    }

    /**
     * @return void
     */
    public function testDetach()
    {
        $user = Sanctum::actingAs($this->user, [PermissionType::TEAM_UPDATE]);

        $team = $user->teams()->save(factory(Team::class)->make());
        $member = $team->users()->save(factory(User::class)->make());

        $this->assertCount(2, $team->users);

        $this->json('DELETE', 'api/teams/'.$team->id.'/users/'.$member->id)
            ->assertNoContent();

        $this->assertCount(1, $team->refresh()->users);
    }

    /**
     * @return void
     */
    public function testGuestAttach()
    {
        $user = Sanctum::actingAs($this->user, [PermissionType::TEAM_UPDATE]);

        $team = factory(Team::class)->create();

        $response = $this->json('POST', 'api/teams/'.$team->id.'/users', [
            'user_ids' => $user->id,
        ])
            ->assertForbidden();

        $this->assertEquals(
            ErrorType::USER_NOT_IN_TEAM,
            $response->exception->getCode()
        );
    }

    /**
     * @return void
     */
    public function testGuestDetach()
    {
        $user = Sanctum::actingAs($this->user, [PermissionType::TEAM_UPDATE]);

        $team = factory(Team::class)->create();

        $response = $this->json('DELETE', 'api/teams/'.$team->id.'/users/'.$user->id)
            ->assertForbidden();

        $this->assertEquals(
            ErrorType::USER_NOT_IN_TEAM,
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

        $response = $this->json('POST', 'api/teams/'.$team->id.'/users')
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

        $response = $this->json('DELETE', 'api/teams/'.$team->id.'/users/'.$user->id)
            ->assertForbidden();

        $this->assertEquals(
            ErrorType::PERMISSION_DENIED,
            $response->exception->getCode()
        );
    }
}
