<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static POOL ()
 * @method static static QATAR_FINAL ()
 * @method static static SEMI_FINAL ()
 * @method static static FINAL ()
 */
final class PlayOffTypeEnum extends Enum
{
    const POOL  =   "POOL";
    const QATAR_FINAL  =   "QATAR_FINAL";
    const SEMI_FINAL  =   "SEMI_FINAL";
    const FINAL  =   "FINAL";
}
