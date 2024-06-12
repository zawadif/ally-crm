<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static CHAT()
 * @method static static SUPPORT()
 * @method static static MATCH()
 * @method static static SCORE()
 * @method static static CHALLENGE()
 * @method static static UPDATE()
 * @method static static PLAY_OFF()
 * @method static static SUBSCRIPTION()
 * @method static static PAYMENT_REQUEST()
 */
final class NotificationTypeEnum extends Enum
{
    const CHAT =   "CHAT";
    const SUPPORT =   "SUPPORT";
    const MATCH =   "MATCH";
    const SCORE =   "SCORE";
    const CHALLENGE =   "CHALLENGE";
    const UPDATE =   "UPDATE";
    const PLAY_OFF =   "PLAY_OFF";
    const SUBSCRIPTION =   "SUBSCRIPTION";
    const PAYMENT_REQUEST =   "PAYMENT_REQUEST";
    const ADMIN_PANEL =   "ADMIN_PANEL";
    const ADMIN_CHAT =   "ADMIN_CHAT";
    const SUPPORT_CHAT =   "SUPPORT_CHAT";
    const USER_CHAT =   "USER_CHAT";
}
