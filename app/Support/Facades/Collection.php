<?php

namespace App\Support\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \App\Support\Collection make($items = [])
 *
 * @see \App\Support\Collection
 */
class Collection extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'collection';
    }
}
