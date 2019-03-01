<?php

namespace Railroad\Doctrine\Tests\Fixtures;

use Railroad\Doctrine\Tests\Fixtures\UserEntity;
use Railroad\Doctrine\Tests\Fixtures\UserEntityInterface;
use Railroad\Doctrine\Tests\Fixtures\UserProviderInterface;

class UserProvider implements UserProviderInterface
{
    public function getUserById(int $id): UserEntityInterface
    {
        return new UserEntity($id);
    }

    public function getUserId(UserEntityInterface $user): int
    {
        return $user->getId();
    }
}
