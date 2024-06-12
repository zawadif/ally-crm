<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static USER_ASSIGN()
 * @method static static ADMIN_ASSIGN()
 */
final class RegionAssignEnum extends Enum
{
    const USER_ASSIGN =  'USER_ASSIGN';
    const ADMIN_ASSIGN =   'ADMIN_ASSIGN';
}
