<?php

namespace Railroad\Doctrine\Tests\Fixtures;

use Railroad\Doctrine\Tests\Fixtures\UserEntityInterface;

interface UserProviderInterface
{
    public function getUserById(int $id): UserEntityInterface;

    public function getUserId(UserEntityInterface $user): int;
}
