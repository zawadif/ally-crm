<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static PENDING()
 * @method static static ACCEPTED()
 * @method static static REJECTED()
 * @method static static CANCEL ()
 * @method static static SCORE_UPDATE ()
 * @method static static ACCEPT_SCORE ()
 * @method static static UPDATE_COURT ()
 * @method static static WITHDRAWN  ()
 * @method static static PLAYED()
 * @method static static DISPUTED()
 * @method static static DROPPED()
 * @method static static EXPIRED()
 * @method static static COMPLETED()
 */
final class MatchStatusEnum extends Enum
{
    const PENDING =   "PENDING";
    const ACCEPTED =   "ACCEPTED";
    const REJECTED =   "REJECTED";
    const CANCEL  =   "CANCEL";
    const SCORE_UPDATE  =   "SCORE_UPDATE";
    const SCORE_UPDATED  =   "SCORE_UPDATED";
    const RESCHEDULE  =   "RESCHEDULE";
    const ACCEPT_SCORE   =   "ACCEPT_SCORE";
    const UPDATE_COURT   =   "UPDATE_COURT";
    const WITHDRAWN   =   "WITHDRAWN";
    const PLAYED = "PLAYED";
    const DISPUTED = "DISPUTED";
    const DROPPED =   "DROPPED";
    const EXPIRED = "EXPIRED";
    const COMPLETED =   "COMPLETED";
}