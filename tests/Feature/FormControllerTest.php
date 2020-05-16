<?php

namespace Tests\Feature;

use App\Models\Form;
use App\Models\Team;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class FormControllerTest extends TestCase
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
    public function testShow()
    {
        $team = $this->user->teams()->save(factory(Team::class)->make());
        $form = $team->forms()->save(factory(Form::class)->make());

        $this->json('GET', 'api/forms/1', [
            'relations' => '',
        ])
            ->assertStatus(Response::HTTP_OK)
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
        $team = $this->user->teams()->save(factory(Team::class)->make());
        $team->forms()->save(factory(Form::class)->make());

        $form = factory(Form::class)->make([
            'name' => 'New Form',
        ])->toArray();

        $this->json('PATCH', 'api/forms/1', $form)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'data' => $form,
            ]);

        $this->assertDatabaseHas('forms', $form);
    }

    /**
     * @return void
     */
    public function testUpdateDuplicate()
    {
        $team = $this->user->teams()->save(factory(Team::class)->make());
        $team->forms()->saveMany(factory(Form::class, 2)->make());

        $form = factory(Form::class)->make([
            'name' => 'New Form 1',
        ])->toArray();

        $this->json('PATCH', 'api/forms/1', $form)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'data' => $form,
            ]);

        $form = factory(Form::class)->make([
            'name' => 'Form 2',
        ])->toArray();

        $this->json('PATCH', 'api/forms/1', $form)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure([
                'errors' => [
                    'name',
                ],
            ]);
    }

    /**
     * @return void
     */
    public function testDestroy()
    {
        $team = $this->user->teams()->save(factory(Team::class)->make());
        $form = $team->forms()->save(factory(Form::class)->make());

        $this->json('DELETE', 'api/forms/1')
            ->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDeleted($form);
    }
}
