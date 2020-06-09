<?php

namespace App\Models\Traits;

use App\Models\Form;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * @property Collection $forms
 */
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
}
