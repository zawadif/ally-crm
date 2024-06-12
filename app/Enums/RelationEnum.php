<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static FATHER()
 * @method static static SPOUSE()
 * @method static static BROTHER()
 * @method static static GRANDPARENT()
 * @method static static FRIEND()
 * @method static static CHILD()
 * @method static static MOTHER()
 * @method static static SISTER()
 * @method static static WIFE()
 * @method static static DOCTOR()
 * @method static static NEIGHBOR()
 * @method static static GUARDIAN()
 * @method static static OTHER_RELATIVE()
 * @method static static SIGNIFICANT_OTHER()
 */
final class RelationEnum extends Enum
{
    const FATHER =   "FATHER";
    const SPOUSE =   "SPOUSE";
    const BROTHER = "BROTHER";
    const GRANDPARENT = "GRANDPARENT";
    const FRIEND = "FRIEND";
    const CHILD = "CHILD";
    const MOTHER = "MOTHER";
    const SISTER = "SISTER";
    const NEIGHBOR = "NEIGHBOR";
    const GUARDIAN = "GUARDIAN";
    const WIFE = "WIFE";
    const DOCTOR = "DOCTOR";
    const OTHER_RELATIVE = "OTHER_RELATIVE";
    const SIGNIFICANT_OTHER = "SIGNIFICANT_OTHER";
}
