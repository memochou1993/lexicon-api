<?php

namespace Tests\Feature;

use App\Models\Form;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class TeamFormControllerTest extends TestCase
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
    public function testStore()
    {
        $team = $this->user->teams()->save(factory(Team::class)->make());

        $form = factory(Form::class)->make()->toArray();

        $this->json('POST', 'api/teams/1/forms', $form)
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJson([
                'data' => $form,
            ]);

        $this->assertDatabaseHas('forms', $form);

        $this->assertCount(1, $team->forms);
    }

    /**
     * @return void
     */
    public function testStoreDuplicate()
    {
        $team = $this->user->teams()->save(factory(Team::class)->make());
        $team->forms()->save(factory(Form::class)->make([
            'name' => 'Unique Form',
        ]));

        $form = factory(Form::class)
            ->make([
                'name' => 'Unique Form',
            ])
            ->toArray();

        $this->json('POST', 'api/teams/1/forms', $form)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure([
                'errors' => [
                    'name',
                ],
            ]);

        $this->assertCount(1, $team->forms);
    }
}
