<?php

namespace Railroad\Doctrine\Tests\Fixtures;

interface UserEntityInterface
{
    public function getId(): int;

    public function __toString();
}
