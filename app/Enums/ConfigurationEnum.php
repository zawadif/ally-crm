<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static ConfigBoolean()
 * @method static static ConfigString()
 * @method static static ConfigNumber()
 */
final class ConfigurationEnum extends Enum
{
    const ConfigBoolean =   "ConfigBoolean";
    const ConfigString =   "ConfigString";
    const ConfigNumber =   "ConfigNumber";
}
