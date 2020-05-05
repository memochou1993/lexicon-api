<?php

namespace App\Traits;

use App\Models\Language;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

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
