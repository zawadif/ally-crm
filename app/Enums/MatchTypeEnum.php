<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static PLAYOFF()
 * @method static static CHALLENGE()
 * @method static static PROPOSAL()
 */
final class MatchTypeEnum extends Enum
{
    const PLAYOFF = 'PLAYOFF';
    const CHALLENGE =  'CHALLENGE';
    const PROPOSAL = 'PROPOSAL';
}
