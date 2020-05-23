<?php

namespace App\Models\Traits;

use App\Models\Form;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

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
