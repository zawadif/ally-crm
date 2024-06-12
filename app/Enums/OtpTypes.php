<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static EMAIL()
 * @method static static PHONE_NUMBER()
 * @method static static FORGET_PASSWORD()
 */
final class OtpTypes extends Enum
{
    // for api side
    const SIGNUP_NUMBER_OTP   = 'SIGNUP_NUMBER_OTP';
    const SIGNUP_EMAIL_OTP  = 'SIGNUP_EMAIL_OTP';
    const RESET_NUMBER_OTP ='RESET_NUMBER_OTP';
    const RESET_EMAIL_OTP  ='RESET_EMAIL_OTP';

    // For server side
    const EMAIL  = 'EMAIL';
    const PHONE_NUMBER  = 'PHONE_NUMBER';
    const FORGET_PASSWORD ='FORGET_PASSWORD';
}