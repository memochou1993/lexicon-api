<?php

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        collect(config('permission.roles'))->each(function ($item) {
            $role = Role::create([
                'name' => $item['name']
            ]);

            $role->permissions()->sync(
                Permission::whereIn('name', $item['permissions'])->pluck('id')
            );
        });
    }
}
