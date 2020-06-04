<?php

namespace App\Models\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait HasUsers
{
    /**
     * Get all of the users for the model.
     *
     * @return MorphToMany
     */
    public function users(): MorphToMany
    {
        return $this->morphToMany(User::class, 'model', 'model_has_users');
    }

    /**
     * @return MorphToMany
     */
    public function owners()
    {
        return $this->users()->where('is_owner', true);
    }
}
