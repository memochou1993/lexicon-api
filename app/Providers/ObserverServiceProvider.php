<?php

namespace App\Providers;

use App\Models\Language;
use App\Models\Project;
use App\Observers\LanguageObserver;
use App\Observers\ProjectObserver;
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
        Project::observe(ProjectObserver::class);
        Language::observe(LanguageObserver::class);
    }
}
