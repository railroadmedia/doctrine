<?php

namespace Railroad\Doctrine\Contracts;

interface UserEntityInterface
{
    public function getId(): int;

    public function __toString();
}
