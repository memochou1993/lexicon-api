<?php

use App\Models\Permission;
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
        collect(config('permission.roles'))
            ->pluck('permissions')
            ->flatten()
            ->unique()
            ->each(function ($name) {
                Permission::create([
                    'name' => $name,
                ]);
            });
    }
}
