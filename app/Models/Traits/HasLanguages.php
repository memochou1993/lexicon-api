<?php

namespace App\Models\Traits;

use App\Models\Language;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Facades\Cache;

trait HasLanguages
{
    /**
     * Get all of the languages for the model.
     *
     * @return MorphToMany
     */
    public function languages(): MorphToMany
    {
        return $this->morphToMany(Language::class, 'model', 'model_has_languages');
    }

    /**
     * @return Collection
     */
    public function getCachedLanguages(): Collection
    {
        $cacheKey = sprintf('%s:%d:languages', $this->getTable(), $this->getKey());

        return Cache::sear($cacheKey, fn() => $this->languages);
    }

    /**
     * @return bool
     */
    public function forgetCachedLanguages(): bool
    {
        $cacheKey = sprintf('%s:%d:languages', $this->getTable(), $this->getKey());

        return Cache::forget($cacheKey);
    }
}
