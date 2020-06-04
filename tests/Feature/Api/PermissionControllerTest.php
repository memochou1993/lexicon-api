<?php

namespace Tests\Feature\Api;

use App\Enums\PermissionType;
use App\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PermissionControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function testIndex()
    {
        Sanctum::actingAs($this->user, [PermissionType::PERMISSION_VIEW_ANY]);

        factory(Permission::class)->create();

        $this->json('GET', 'api/permissions', [
            'relations' => '',
        ])
            ->assertOk()
            ->assertJsonStructure([
                'data',
            ]);
    }

    /**
     * @return void
     */
    public function testShow()
    {
        Sanctum::actingAs($this->user, [PermissionType::PERMISSION_VIEW]);

        /** @var Permission $permission */
        $permission = factory(Permission::class)->create();

        $this->json('GET', 'api/permissions/'.$permission->id, [
            'relations' => '',
        ])
            ->assertOk()
            ->assertJsonStructure([
                'data',
            ])
            ->assertJson([
                'data' => $permission->toArray(),
            ]);
    }
}
