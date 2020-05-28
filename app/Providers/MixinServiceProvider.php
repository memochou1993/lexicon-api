<?php

namespace App\Providers;

use App\Mixins\CollectionMixin;
use App\Mixins\FactoryMixin;
use App\Mixins\ResponseMixin;
use Illuminate\Database\Eloquent\FactoryBuilder;
use Illuminate\Routing\ResponseFactory;
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
        FactoryBuilder::mixin(new FactoryMixin());
        ResponseFactory::mixin(new ResponseMixin());
    }
}
