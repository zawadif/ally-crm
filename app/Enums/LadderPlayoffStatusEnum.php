<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static ROUND_OF_16()
 * @method static static QUARTER_FINAL()
 * @method static static SEMI_FINAL()
 * @method static static FINAL()
 * @method static static WINNER()
 */
final class LadderPlayoffStatusEnum extends Enum
{
    const ROUND_OF_16 =   "ROUND_OF_16";
    const QUARTER_FINAL =   "QUARTER_FINAL";
    const SEMI_FINAL =   "SEMI_FINAL";
    const FINAL =   "FINAL";
    const WINNER =   "WINNER";
}
