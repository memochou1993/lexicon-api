<?php

namespace App\Exceptions;

use App\Enums\ErrorType;
use Illuminate\Auth\Access\AuthorizationException;

class UserNotInProjectException extends AuthorizationException
{
    /**
     * Create a new authorization exception instance.
     */
    public function __construct()
    {
        parent::__construct(__('error.access'), ErrorType::USER_NOT_IN_PROJECT);
    }
}
