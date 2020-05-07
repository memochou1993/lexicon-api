<?php

namespace App\Providers;

use App\Mixins\CollectionMixin;
use App\Mixins\FactoryBuilderMixin;
use Illuminate\Database\Eloquent\FactoryBuilder;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use ReflectionException;

class MixinServiceProvider extends ServiceProvider
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
     * @throws ReflectionException
     */
    public function boot()
    {
        Collection::mixin(new CollectionMixin());
        FactoryBuilder::mixin(new FactoryBuilderMixin());
    }
}
