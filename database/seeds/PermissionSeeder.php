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
        $permissions = collect(config('permission.permissions'));

        $permissions
            ->flatten()
            ->unique()
            ->each(function ($permission) {
                Permission::create([
                    'name' => $permission,
                ]);
            });

        $permissions
            ->each(function ($permissions, $role) {
                $permission_ids = Permission::whereIn('name', $permissions)->pluck('id');

                Role::create([
                    'name' => $role
                ])->permissions()->sync($permission_ids);
            });

    }
}
