<?php

namespace Railroad\Doctrine\Tests\Fixtures;

use Doctrine\ORM\Mapping as ORM;
use Railroad\Doctrine\Tests\Fixtures\UserEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="addresses")
 */
class Address
{
    /**
     * @var int
     *
     * @ORM\Id @ORM\GeneratedValue @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @var int
     *
     * @ORM\Column(type="user_id", name="user_id", nullable=true)
     */
    protected $userId;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return UserEntity|null
     */
    public function getUserId(): ?UserEntity
    {
        return $this->userId;
    }

    /**
     * @param UserEntity $user
     *
     * @return Address
     */
    public function setUserId($user): self
    {
        $this->userId = $user;

        return $this;
    }
}
