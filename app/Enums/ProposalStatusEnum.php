<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static ACCEPTED()
 * @method static static PENDING()
 * @method static static EXPIRED()
 * @method static static WITHDRAWN()
 */
final class ProposalStatusEnum extends Enum
{
    const ACCEPTED =   "ACCEPTED";
    const PENDING =   "PENDING";
    const EXPIRED = "EXPIRED";
    const WITHDRAWN = "WITHDRAWN";
}
