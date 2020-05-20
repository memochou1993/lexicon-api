<?php

namespace App\Console\Commands;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;

class InitProject extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'project:init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize the project';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->migrate();
        $this->seedPermission();
        $this->seedRole();
        $this->setupAdmin();
    }

    /**
     * @return void
     */
    private function migrate()
    {
        $this->call('migrate', [
            '--force' => true,
        ]);
    }

    /**
     * @return void
     */
    private function seedPermission()
    {
        if (Permission::count()) {
            return;
        }

        $this->call('db:seed', [
            '--force' => true,
            '--class' => 'PermissionSeeder',
        ]);
    }

    /**
     * @return void
     */
    private function seedRole()
    {
        if (Role::count()) {
            return;
        }

        $this->call('db:seed', [
            '--force' => true,
            '--class' => 'RoleSeeder',
        ]);
    }

    /**
     * @return void
     */
    private function setupAdmin()
    {
        $admin = Role::where('name', config('permission.roles.admin.name'))->first();

        if ($admin->users->count()) {
            return;
        }

        $name = $this->ask('Enter the admin name', 'Admin');
        $email = $this->ask('Enter the admin email address', 'admin@email.com');
        $password = $this->askForPassword();

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ]);

        $user->roles()->attach($admin);

        $this->info('Admin account created successfully.');
    }

    /**
     * @return string
     */
    private function askForPassword()
    {
        $password = $this->secret('Enter the admin password');

        if (! $password) {
            $this->error('Password cannot be empty.');

            return $this->askForPassword();
        }

        if ($password !== $this->secret('Confirm the admin password')) {
            $this->error('Passwords do not match.');

            return $this->askForPassword();
        }

        return $password;
    }
}
