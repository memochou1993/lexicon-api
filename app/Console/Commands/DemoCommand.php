<?php

namespace App\Console\Commands;

use App\Models\Form;
use App\Models\Key;
use App\Models\Language;
use App\Models\Project;
use App\Models\Team;
use App\Models\User;
use App\Models\Value;
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

        /** @var Team $team */
        $team = $user->teams()->create([
            'name' => 'New Team',
        ]);

        /** @var Project $project */
        $project = $team->projects()->create([
            'name' => 'New Project',
        ]);

        $user->projects()->attach($project);

        /** @var Language $en */
        $en = $team->languages()->create([
            'name' => 'en',
        ]);

        $project->languages()->attach($en);

        /** @var Form $enDefault */
        $enDefault = $team->forms()->create([
            'name' => 'default',
            'range_min' => 0,
            'range_max' => 0,
        ]);

        /** @var Form $enSingular */
        $enSingular = $team->forms()->create([
            'name' => 'singular',
            'range_min' => 1,
            'range_max' => 1,
        ]);

        /** @var Form $enPlural */
        $enPlural = $team->forms()->create([
            'name' => 'plural',
            'range_min' => 2,
            'range_max' => '*',
        ]);

        $en->forms()->sync([$enDefault->id, $enSingular->id, $enPlural->id]);

        /** @var Language $zh */
        $zh = $team->languages()->create([
            'name' => 'zh',
        ]);

        $project->languages()->attach($zh);

        /** @var Form $zhDefault */
        $zhDefault = $team->forms()->create([
            'name' => 'default',
        ]);

        $zh->forms()->sync([$zhDefault->id]);

        $createKey = function (
            $keyName,
            $enText,
            $zhText
        ) use (
            $project,
            $en,
            $zh,
            $enDefault,
            $zhDefault
        ) {
            /** @var Key $key */
            $key = $project->keys()->create([
                'name' => $keyName,
            ]);

            /** @var Value $enValue */
            $enValue = $key->values()->create([
                'text' => $enText,
            ]);

            $enValue->languages()->attach($en);
            $enValue->forms()->attach($enDefault);

            /** @var Value $zhValue */
            $zhValue = $key->values()->create([
                'text' => $zhText,
            ]);

            $zhValue->languages()->attach($zh);
            $zhValue->forms()->attach($zhDefault);
        };

        $createKey('project.name', 'New Project', '我的專案');
        $createKey('action.sync', 'Sync Language Files', '同步語系檔');
        $createKey('action.clear', 'Clear Language Files', '清除語系檔');
        $createKey('action.dump', 'Dump Language File', '查看語系檔');
        $createKey('table.header.code_in_blade_template', 'PHP Code in Blade Template', '模板引擎程式碼');
        $createKey('table.header.translation', 'Translation', '翻譯');
        $createKey('table.header.code_in_language_file', 'PHP Code in Language File', '語系檔程式碼');

        $project->hooks()->create([
            'url' => config('app.url').'/api/'.config('lexicon.path'),
            'events' => [
                'sync',
            ],
        ]);

        $this->info('API Token: '.$project->getSetting('api_key'));

        $this->info('Personal Access Token: '.$user->createToken('demo')->plainTextToken);
    }
}
