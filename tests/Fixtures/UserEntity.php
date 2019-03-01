<?php

namespace Railroad\Doctrine\Tests\Fixtures;

use Railroad\Doctrine\Tests\Fixtures\UserEntityInterface;

// not mapped, here
class UserEntity implements UserEntityInterface
{
    protected $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function __toString()
    {
        /*
        method needed by UnitOfWork
        https://www.doctrine-project.org/projects/doctrine-orm/en/2.6/cookbook/custom-mapping-types.html
        */

        return (string) $this->id;
    }
}
