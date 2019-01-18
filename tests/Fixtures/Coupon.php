<?php

namespace Railroad\Doctrine\Tests\Fixtures;

use Carbon\Carbon;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="coupons")
 */
class Coupon
{
    use TimestampableEntity;

    /**
     * @ORM\Id @ORM\GeneratedValue @ORM\Column(type="integer")
     * @var int
     */
    protected $id;

    /**
     * @var Carbon
     *
     * @ORM\Column(type="time", name="used_at_time", nullable=true)
     */
    protected $usedAtTime;

    /**
     * @var Carbon
     *
     * @ORM\Column(type="date", name="used_at_date", nullable=true)
     */
    protected $usedAtDate;

    /**
     * @var Carbon
     *
     * @ORM\Column(type="datetime", name="used_at_datetime", nullable=true)
     */
    protected $usedAtDateTime;

    /**
     * @var Carbon
     *
     * @ORM\Column(type="datetimetz", name="used_at_datetimetz", nullable=true)
     */
    protected $usedAtDateTimeTz;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Carbon|null
     */
    public function getUsedAtTime(): ?Carbon
    {
        return $this->usedAtTime;
    }

    /**
     * @param Carbon $usedAtTime
     *
     * @return Coupon
     */
    public function setUsedAtTime($usedAtTime): self
    {
        $this->usedAtTime = $usedAtTime;

        return $this;
    }

    /**
     * @return Carbon|null
     */
    public function getUsedAtDate()
    {
        return $this->usedAtDate;
    }

    /**
     * @param Carbon $usedAtDate
     *
     * @return Coupon
     */
    public function setUsedAtDate($usedAtDate): self
    {
        $this->usedAtDate = $usedAtDate;

        return $this;
    }

    /**
     * @return Carbon|null
     */
    public function getUsedAtDateTime()
    {
        return $this->usedAtDateTime;
    }

    /**
     * @param Carbon $usedAtDateTime
     *
     * @return Coupon
     */
    public function setUsedAtDateTime($usedAtDateTime): self
    {
        $this->usedAtDateTime = $usedAtDateTime;

        return $this;
    }

    /**
     * @return Carbon|null
     */
    public function getUsedAtDateTimeTz()
    {
        return $this->usedAtDateTimeTz;
    }

    /**
     * @param Carbon $usedAtDateTimeTz
     *
     * @return Coupon
     */
    public function setUsedAtDateTimeTz($usedAtDateTimeTz): self
    {
        $this->usedAtDateTimeTz = $usedAtDateTimeTz;

        return $this;
    }
}
