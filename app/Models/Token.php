<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphTo;
use Laravel\Sanctum\PersonalAccessToken;

/**
 * @property int $id
 * @property string $token
 */
class Token extends PersonalAccessToken
{
    /**
     * Get the model that the token belongs to.
     *
     * @return MorphTo
     */
    public function model(): MorphTo
    {
        return $this->morphTo('model');
    }

    /**
     * Get the model that the token belongs to.
     *
     * @return MorphTo
     */
    public function tokenable()
    {
        return $this->model();
    }
}
