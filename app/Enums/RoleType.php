<?php

namespace App\Enums;

use App\Enums\Contracts\EnumContract;

final class RoleType extends AbstractEnum implements EnumContract
{
    const ADMINISTRATOR = 'Administrator';
    const DEVELOPER = 'Developer';
    const MAINTAINER = 'Maintainer';
}
