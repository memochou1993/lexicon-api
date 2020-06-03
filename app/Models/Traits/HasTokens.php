<?php

namespace App\Models\Traits;

use App\Models\Token;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasTokens
{
    /**
     * Get the tokens that belong to model.
     *
     * @return MorphMany
     */
    public function tokens()
    {
        return $this->morphMany(Token::class, 'model');
    }
}
