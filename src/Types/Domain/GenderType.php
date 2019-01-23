<?php

namespace Railroad\Doctrine\Types\Domain;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

class GenderType extends StringType
{
    const GENDER_TYPE = 'gender';

    /**
     * {@inheritdoc}
     */
    public function getDefaultLength(AbstractPlatform $platform)
    {
        return 32;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return self::GENDER_TYPE;
    }
}