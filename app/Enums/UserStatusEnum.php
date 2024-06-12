<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static ACTIVE()
 * @method static static BLOCK()
 * @method static static REGISTRATION_INPROCESS()
 */
final class UserStatusEnum extends Enum
{
    const ACTIVE =  'ACTIVE';
    const BLOCK =   'BLOCK';
    const REGISTRATION_INPROCESS = 'REGISTRATION_INPROCESS';  //this is use when user profile is not complete.
}
