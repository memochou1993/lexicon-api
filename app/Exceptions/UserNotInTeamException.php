<?php

namespace App\Exceptions;

use App\Enums\ErrorType;
use Illuminate\Auth\Access\AuthorizationException;

class UserNotInTeamException extends AuthorizationException
{
    /**
     * Create a new authorization exception instance.
     */
    public function __construct()
    {
        parent::__construct(trans('error.access'), ErrorType::USER_NOT_IN_TEAM);
    }
}
