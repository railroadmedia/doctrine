<?php

namespace Railroad\Doctrine\Types\Domain;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

class TimezoneType extends StringType
{
    const TIMEZONE_TYPE = 'timezone';

    /**
     * {@inheritdoc}
     */
    public function getDefaultLength(AbstractPlatform $platform)
    {
        return 128;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return self::TIMEZONE_TYPE;
    }
}