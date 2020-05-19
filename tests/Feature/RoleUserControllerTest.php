<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class RoleUserControllerTest extends TestCase
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
        $role = $this->user->roles()->save(factory(Role::class)->make());
        $user = factory(User::class)->create();

        $this->assertCount(1, $role->users);

        $this->json('POST', 'api/roles/'.$role->id.'/users', [
            'user_ids' => $user->id,
        ])
            ->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertCount(2, $role->refresh()->users);
    }

    /**
     * @return void
     */
    public function testSync()
    {
        $role = $this->user->roles()->save(factory(Role::class)->make());
        $role->users()->save(factory(User::class)->make());

        $this->assertCount(2, $role->users);

        $this->json('POST', 'api/roles/'.$role->id.'/users', [
            'user_ids' => $role->users()->first()->id,
            'sync' => true,
        ])
            ->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertCount(1, $role->refresh()->users);
    }

    /**
     * @return void
     */
    public function testDetach()
    {
        $role = $this->user->roles()->save(factory(Role::class)->make());
        $user = $role->users()->save(factory(User::class)->make());

        $this->assertCount(2, $role->users);

        $this->json('DELETE', 'api/roles/'.$role->id.'/users/'.$user->id)
            ->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertCount(1, $role->refresh()->users);
    }
}
