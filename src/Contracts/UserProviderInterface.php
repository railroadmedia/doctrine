<?php

namespace Railroad\Doctrine\Contracts;

use Railroad\Doctrine\Contracts\UserEntityInterface;

interface UserProviderInterface
{
    public function getUserById(int $id): ?UserEntityInterface;

    public function getUserId(UserEntityInterface $user): int;
}
