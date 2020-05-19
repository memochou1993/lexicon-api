<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

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
     * @return mixed
     */
    public function handle()
    {
        $this->migrate();
        $this->seed();

        return;
    }

    private function migrate()
    {
        $this->comment('Migrating...');

        Artisan::call('migrate', [
            '--force' => true,
        ]);

        $this->comment('Migrated successfully.');
    }

    private function seed()
    {
        $this->comment('Seeding...');

        if (! User::count()) {
            User::create([
                'name' => env('ADMIN_NAME'),
                'email' => env('ADMIN_EMAIL'),
                'password' => env('ADMIN_PASSWORD'),
            ]);

            Artisan::call('db:seed', [
                '--force' => true,
                '--class' => 'PermissionSeeder',
            ]);
        }

        $this->comment('Seeded successfully.');
    }
}
