<?php

namespace App\Mixins;

use Closure;
use Illuminate\Support\Collection;

class CollectionMixin
{
    /**
     * @var Collection
     */
    private Collection $items;

    /**
     * @return Closure
     */
    public function explode()
    {
        return function (string $delimiter) {
            return collect($this->items)->flatMap(function ($item) use ($delimiter) {
                return explode($delimiter, str_replace(' ', '', $item));
            });
        };
    }
}
