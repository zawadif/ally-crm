<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static ACTIVE()
 * @method static static BLOCK()
 * @method static static REGISTRATION_INPROCESS()
 */
final class CategoryStatusEnum extends Enum
{
    const ACTIVE =  'ACTIVE';
    const INACTIVE =   'INACTIVE';
}
