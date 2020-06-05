<?php

namespace App\Support;

use Illuminate\Support\Collection as BaseCollection;

class Collection extends BaseCollection
{
    /**
     * Create a new collection instance if the value isn't one already.
     *
     * @param  mixed  $items
     * @return static
     */
    public static function make($items = [])
    {
        return new static($items);
    }

    /**
     * @param  string  $delimiter
     * @return static
     */
    public function explode(string $delimiter)
    {
        $collection = new static($this->items);

        return $collection->flatMap(function ($item) use ($delimiter) {
            return explode($delimiter, $item);
        });
    }

    /**
     * @return static
     */
    public function trim()
    {
        $collection = new static($this->items);

        return $collection->map(function ($item) {
            return trim($item);
        });
    }
}
