<?php

namespace App\Providers;

use App\Models\Project;
use App\Observers\ProjectObserver;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
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
        if (Auth::guard()->user() || App::environment() === 'testing') {
            Project::observe(ProjectObserver::class);
        }
    }
}
