<?php

namespace App\Enums;

final class DefaultEnum extends Enum
{
    const NO=0;
    const YES=1;

    static function descriptions(): array
    {
        return [
            self::NO=>'否',
            self::YES=>'是',
        ];
    }

}
