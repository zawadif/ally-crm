<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static Staging()
 * @method static static Acceptance()
 * @method static static Production()
 */
final class ServerEnum extends Enum
{
    const Local = 'local';
    const Staging = 'staging';
    const Acceptance = 'acceptance';
    const Production = 'production';
}
