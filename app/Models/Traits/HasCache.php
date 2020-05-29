<?php

namespace App\Models\Traits;

use Closure;
use Illuminate\Support\Facades\Cache;

trait HasCache
{
    /**
     * @return string
     */
    public function cacheKey()
    {
        $indicators = [
            class_basename($this),
            $this->getKey()
        ];

        $cacheKey = collect($indicators)->filter()->implode(':');

        return strtolower($cacheKey);
    }

    /**
     * @param  Closure  $callback
     * @param  mixed  $ttl
     * @return mixed
     */
    public function remember(Closure $callback, $ttl = null)
    {
        return Cache::remember($this->cacheKey(), $ttl, $callback);
    }

    /**
     * @return bool
     */
    public function forget()
    {
        return Cache::forget($this->cacheKey());
    }
}
