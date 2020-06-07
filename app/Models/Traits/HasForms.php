<?php

namespace App\Models\Traits;

use App\Models\Form;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Facades\Cache;

trait HasForms
{
    /**
     * Get all of the forms for the model.
     *
     * @return MorphToMany
     */
    public function forms(): MorphToMany
    {
        return $this->morphToMany(Form::class, 'model', 'model_has_forms');
    }

    /**
     * @return Collection
     */
    public function getCachedForms(): Collection
    {
        $cacheKey = sprintf('%s:%d:forms', $this->getTable(), $this->getKey());

        return Cache::sear($cacheKey, fn() => $this->forms);
    }

    /**
     * @return bool
     */
    public function forgetCachedForms(): bool
    {
        $cacheKey = sprintf('%s:%d:forms', $this->getTable(), $this->getKey());

        return Cache::forget($cacheKey);
    }
}
