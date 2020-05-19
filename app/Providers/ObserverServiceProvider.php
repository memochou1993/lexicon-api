<?php

namespace App\Providers;

use App\Models\Language;
use App\Models\Project;
use App\Models\Role;
use App\Models\Team;
use App\Models\Value;
use App\Observers\LanguageObserver;
use App\Observers\ProjectObserver;
use App\Observers\RoleObserver;
use App\Observers\TeamObserver;
use App\Observers\ValueObserver;
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
        Role::observe(RoleObserver::class);
        Team::observe(TeamObserver::class);
        Project::observe(ProjectObserver::class);
        Language::observe(LanguageObserver::class);
        Value::observe(ValueObserver::class);
    }
}
