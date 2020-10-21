<?php

namespace App\Console\Commands;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Console\Command;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lexicon:init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the Lexicon server';

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
     * @return int
     */
    public function handle()
    {
        $this->migrate();
        $this->seedPermission();
        $this->seedRole();
        $this->setupAdmin();

        return 1;
    }

    /**
     * @return void
     */
    private function migrate()
    {
        $this->call('migrate');
    }

    /**
     * @return void
     */
    private function seedPermission()
    {
        if (Permission::query()->count()) {
            return;
        }

        $this->callSilent('db:seed', [
            '--force' => true,
            '--class' => PermissionSeeder::class,
        ]);
    }

    /**
     * @return void
     */
    private function seedRole()
    {
        if (Role::query()->count()) {
            return;
        }

        $this->callSilent('db:seed', [
            '--force' => true,
            '--class' => RoleSeeder::class,
        ]);
    }

    /**
     * @return void
     */
    private function setupAdmin()
    {
        /** @var Role $admin */
        $admin = Role::query()->where('name', config('permission.roles.admin.name'))->first();

        if ($admin->users()->count()) {
            return;
        }

        $name = $this->ask('Enter admin name', 'Admin');
        $email = $this->ask('Enter admin email address', 'admin@lexicon.com');
        $password = $this->askForPassword();

        /** @var User $user */
        $user = User::query()->create([
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
        $password = $this->secret('Enter admin password');

        if (! $password) {
            $this->error('Password cannot be empty.');

            return $this->askForPassword();
        }

        if ($password !== $this->secret('Enter admin password again')) {
            $this->error('Passwords do not match.');

            return $this->askForPassword();
        }

        return $password;
    }
}
