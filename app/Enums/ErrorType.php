<?php

namespace App\Enums;

use App\Enums\Contracts\EnumContract;

final class ErrorType extends AbstractEnum implements EnumContract
{
    const PERMISSION_DENIED = 403.1;
    const USER_NOT_IN_TEAM = 403.2;
    const USER_NOT_IN_PROJECT = 403.3;
}
