<?php

namespace Railroad\Doctrine\Types\Carbon;

use Carbon\Carbon;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\DateTimeTzType;

class CarbonDateTimeTimezoneType extends DateTimeTzType
{
    /**
     * @param $value
     * @param AbstractPlatform $platform
     * @return bool|Carbon|\DateTime|false|mixed
     * @throws ConversionException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return $value;
        }

        return Carbon::instance(parent::convertToPHPValue($value, $platform));
    }
}
