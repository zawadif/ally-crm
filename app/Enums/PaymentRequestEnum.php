<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static PENDING()
 * @method static static ACCEPTED()
 * @method static static REJECTED()
 */
final class PaymentRequestEnum extends Enum
{

    const PENDING = 'PENDING';
    const REGISTERED = 'REGISTERED';
    const PURCHASED = 'PURCHASED';
    const REJECTED = 'REJECTED';
}