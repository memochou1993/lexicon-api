<?php

namespace App\Exceptions;

use App\Enums\ErrorType;
use Illuminate\Auth\Access\AuthorizationException;

class PermissionDeniedException extends AuthorizationException
{
    /**
     * Create a new authorization exception instance.
     */
    public function __construct()
    {
        parent::__construct(__('error.permission'), ErrorType::PERMISSION_DENIED);
    }
}
