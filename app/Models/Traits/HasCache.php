<?php

namespace App\Models\Traits;

use Closure;
use Illuminate\Support\Facades\Cache;

trait HasCache
{
    /**
     * @return string
     */
    public function cacheKey(): string
    {
        $indicators = [
            class_basename($this),
            $this->getKey()
        ];

        $cacheKey = collect($indicators)->filter()->implode(PATH_SEPARATOR);

        return strtolower($cacheKey);
    }

    /**
     * @param  Closure  $callback
     * @param  mixed  $ttl
     * @return self
     */
    public function remember(Closure $callback, $ttl = null): self
    {
        return Cache::remember($this->cacheKey(), $ttl, $callback);
    }

    /**
     * @return bool
     */
    public function forget(): bool
    {
        return Cache::forget($this->cacheKey());
    }
}
