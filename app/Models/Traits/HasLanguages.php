<?php

namespace App\Models\Traits;

use App\Models\Language;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * @property Collection $languages
 */
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
}
