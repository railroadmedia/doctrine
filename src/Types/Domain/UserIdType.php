<?php

namespace Railroad\Doctrine\Types\Domain;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\IntegerType;
use Railroad\Doctrine\Contracts\UserProviderInterface;

class UserIdType extends IntegerType
{
    const USER_ID_TYPE = 'user_id';

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return $platform->getUnsignedDeclaration($fieldDeclaration);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value !== null) {

            $userProvider = app()->make(UserProviderInterface::class);

            return $userProvider->getUserById($value);
        }
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value !== null) {

            $userProvider = app()->make(UserProviderInterface::class);

            return $userProvider->getUserId($value);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return self::USER_ID_TYPE;
    }
}
