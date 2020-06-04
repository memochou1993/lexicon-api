<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphTo;
use Laravel\Sanctum\PersonalAccessToken;

/**
 * @property int $id
 */
class Token extends PersonalAccessToken
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'model_has_tokens';

    /**
     * Get the model that the token belongs to.
     *
     * @return MorphTo
     */
    public function model()
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
