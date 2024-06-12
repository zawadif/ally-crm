<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static SYSTEM_CREATED()
 * @method static static ADMIN_CREATED()
 */
final class RoleTypeEnum extends Enum
{
    const SYSTEM_CREATED = "SYSTEM_CREATED";
    const ADMIN_CREATED =   "ADMIN_CREATED";
}
