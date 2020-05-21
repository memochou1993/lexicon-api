<?php

namespace Tests\Feature;

use App\Models\Form;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TeamFormControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function testStore()
    {
        $user = Sanctum::actingAs($this->user, ['update-team']);

        $team = $user->teams()->save(factory(Team::class)->make());

        $data = factory(Form::class)->make()->toArray();

        $this->json('POST', 'api/teams/'.$team->id.'/forms', $data)
            ->assertCreated()
            ->assertJson([
                'data' => $data,
            ]);

        $this->assertDatabaseHas('forms', $data);

        $this->assertCount(1, $team->forms);
    }

    /**
     * @return void
     */
    public function testStoreDuplicate()
    {
        $user = Sanctum::actingAs($this->user, ['update-team']);

        $team = $user->teams()->save(factory(Team::class)->make());
        $team->forms()->save(factory(Form::class)->make([
            'name' => 'Unique Form',
        ]));

        $data = factory(Form::class)->make([
            'name' => 'Unique Form',
        ])->toArray();

        $this->json('POST', 'api/teams/'.$team->id.'/forms', $data)
            ->assertJsonValidationErrors([
                'name',
            ]);

        $this->assertCount(1, $team->forms);
    }

    /**
     * @return void
     */
    public function testGuestCreate()
    {
        Sanctum::actingAs($this->user, ['update-team']);

        $team = factory(Team::class)->create();

        $data = factory(Form::class)->make()->toArray();

        $this->json('POST', 'api/teams/'.$team->id.'/forms', $data)
            ->assertForbidden();
    }

    /**
     * @return void
     */
    public function testCreateWithoutPermission()
    {
        $user = Sanctum::actingAs($this->user);

        $team = $user->teams()->save(factory(Team::class)->make());

        $data = factory(Form::class)->make()->toArray();

        $this->json('POST', 'api/teams/'.$team->id.'/forms', $data)
            ->assertForbidden();
    }
}
