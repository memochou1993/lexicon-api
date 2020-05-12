<?php

namespace App\Providers;

use App\Models\Language;
use App\Models\Project;
use App\Models\Team;
use App\Observers\LanguageObserver;
use App\Observers\ProjectObserver;
use App\Observers\TeamObserver;
use Illuminate\Support\ServiceProvider;

class ObserverServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Team::observe(TeamObserver::class);
        Project::observe(ProjectObserver::class);
        Language::observe(LanguageObserver::class);
    }
}
