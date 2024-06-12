<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static SEASON()
 * @method static static PLAYOFF()
 */
final class WeekTypeEnum extends Enum
{
    const SEASON =   'SEASON';
    const PLAYOFF =   'PLAYOFF';
}
