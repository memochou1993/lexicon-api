<?php

namespace Tests\Feature;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class TeamUserControllerTest extends TestCase
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
    public function testAttach()
    {
        $team = $this->user->teams()->save(factory(Team::class)->make());
        $user = factory(User::class)->create();

        $this->assertCount(1, $team->users);

        $this->json('POST', 'api/teams/'.$team->id.'/users', [
            'user_ids' => $user->id,
        ])
            ->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertCount(2, $team->refresh()->users);
    }

    /**
     * @return void
     */
    public function testSync()
    {
        $team = $this->user->teams()->save(factory(Team::class)->make());
        $team->users()->save(factory(User::class)->make());

        $this->assertCount(2, $team->users);

        $this->json('POST', 'api/teams/'.$team->id.'/users', [
            'user_ids' => $this->user->id,
            'sync' => true,
        ])
            ->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertCount(1, $team->refresh()->users);
    }

    /**
     * @return void
     */
    public function testAttachForbidden()
    {
        $guest = factory(User::class)->create();
        $team = $guest->teams()->save(factory(Team::class)->make());

        $this->json('POST', 'api/teams/'.$team->id.'/users', [
            'user_ids' => $this->user->id,
        ])
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * @return void
     */
    public function testDetach()
    {
        $team = $this->user->teams()->save(factory(Team::class)->make());
        $user = $team->users()->save(factory(User::class)->make());

        $this->assertCount(2, $team->users);

        $this->json('DELETE', 'api/teams/'.$team->id.'/users/'.$user->id)
            ->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertCount(1, $team->refresh()->users);
    }

    /**
     * @return void
     */
    public function testDetachForbidden()
    {
        $guest = factory(User::class)->create();
        $team = $guest->teams()->save(factory(Team::class)->make());

        $this->json('DELETE', 'api/teams/'.$team->id.'/users/'.$guest->id)
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }
}
