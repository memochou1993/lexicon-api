<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        collect(config('permission.roles'))
            ->each(function ($item) {
                /** @var Role $role */
                $role = Role::query()->create([
                    'name' => $item['name']
                ]);

                $role->permissions()->sync(
                    Permission::query()->whereIn('name', $item['permissions'])->orderBy('id')->get()
                );
            });
    }
}
