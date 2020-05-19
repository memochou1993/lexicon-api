<?php

namespace App\Traits;

use App\Models\Permission;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait HasPermissions
{
    /**
     * Get all of the permissions for the model.
     *
     * @return MorphToMany
     */
    public function permissions(): MorphToMany
    {
        return $this->morphToMany(Permission::class, 'model', 'model_has_permissions');
    }
}
