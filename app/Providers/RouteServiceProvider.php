<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * The path to the "home" route for your application.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapAppRoutes();

        $this->mapAuthRoutes();

        $this->mapClientRoutes();

        $this->mapUserRoutes();
    }

    /**
     * Define the "app" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapAppRoutes()
    {
        Route::prefix('api')
            ->middleware([
                'api',
                'auth:sanctum',
            ])
            ->namespace($this->namespace.'\Api')
            ->group(base_path('routes/api/app.php'));
    }

    /**
     * Define the "auth" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapAuthRoutes()
    {
        Route::prefix('api/auth')
            ->middleware('api')
            ->namespace($this->namespace.'\Api')
            ->group(base_path('routes/api/auth.php'));
    }

    /**
     * Define the "client" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapClientRoutes()
    {
        Route::prefix('api/client')
            ->middleware([
                'api',
                'client',
            ])
            ->namespace($this->namespace.'\Api\Client')
            ->group(base_path('routes/api/client.php'));
    }

    /**
     * Define the "user" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapUserRoutes()
    {
        Route::prefix('api/user')
            ->middleware([
                'api',
                'auth:sanctum',
            ])
            ->namespace($this->namespace.'\Api\User')
            ->group(base_path('routes/api/user.php'));
    }
}
