<?php

namespace App\Console\Commands;

use App\Models\Language;
use App\Models\Project;
use App\Models\Team;
use App\Models\User;
use Illuminate\Console\Command;

class DemoCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lexicon:demo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Demo the Lexicon server';

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
        $this->call(InstallCommand::class);
        $this->seed();

        return 1;
    }

    /**
     * @return void
     */
    private function seed()
    {
        if (Team::query()->count()) {
            return;
        }

        /** @var User $user */
        $user = User::query()->first();

        $this->info('Personal Access Token: '.$user->createToken('demo')->plainTextToken);

        /** @var Team $team */
        $team = $user->teams()->create([
            'name' => 'New Team',
        ]);

        /** @var Project $project */
        $project = $team->projects()->create([
            'name' => 'New Project',
        ]);

        $this->info('API Token: '.$project->getSetting('api_key'));

        $user->projects()->attach($project);

        $project->languages()->attach(
            /** @var Language $en */
            $en = $team->languages()->create([
                'name' => 'en',
            ])
        );

        $en->forms()->attach(
            $team->forms()->create([
                'name' => 'default',
                'range_min' => 0,
                'range_max' => 0,
            ])
        );

        $en->forms()->attach(
            $team->forms()->create([
                'name' => 'singular',
                'range_min' => 1,
                'range_max' => 1,
            ])
        );

        $en->forms()->attach(
            $team->forms()->create([
                'name' => 'plural',
                'range_min' => 2,
                'range_max' => '*',
            ])
        );

        $project->languages()->attach(
        /** @var Language $tw */
            $tw = $team->languages()->create([
                'name' => 'tw',
            ])
        );

        $tw->forms()->attach(
            $team->forms()->create([
                'name' => 'default',
            ])
        );

        $project->hooks()->create([
            'url' => config('app.url').'/api/'.config('lexicon.path'),
            'events' => [
                'sync',
            ],
        ]);
    }
}
