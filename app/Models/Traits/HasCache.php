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
        return sprintf('%s:%d', $this->getTable(), $this->getKey());
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
