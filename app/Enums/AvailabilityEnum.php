<?php

namespace App\Enums;

final class AvailabilityEnum extends Enum
{
    const DISABLE=0;
    const ENABLE=1;

    static function descriptions(): array
    {
        return [
            self::DISABLE=>'禁用',
            self::ENABLE=>'正常',
        ];
    }

}
