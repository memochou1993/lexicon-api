<?php

namespace App\Models;

use Laravel\Sanctum\PersonalAccessToken;

class Token extends PersonalAccessToken
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'personal_access_tokens'; // TODO: change table name
}
