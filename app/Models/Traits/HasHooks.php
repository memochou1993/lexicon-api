<?php

namespace App\Models\Traits;

use App\Models\Hook;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasHooks
{
    /**
     * Get the hooks that belong to model.
     *
     * @return MorphMany
     */
    public function hooks()
    {
        return $this->morphMany(Hook::class, 'model');
    }
}
