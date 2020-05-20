<?php

use App\Models\Permission;
use App\Models\Role;
use App\Traits\HasStaticAttributes;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    use HasStaticAttributes;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = collect(config('permission.roles'));

        $roles->pluck('permissions')->flatten()->unique()->each(function ($name) {
            Permission::create([
                'name' => $name,
            ]);
        });

        $roles->each(function ($role) {
            $permission_ids = Permission::whereIn('name', $role['permissions'])->pluck('id');

            Role::create([
                'name' => $role['name']
            ])->permissions()->sync($permission_ids);
        });
    }
}
