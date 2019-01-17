<?php

namespace Railroad\Doctrine\Tests\Fixtures;

use Carbon\Carbon;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="users")
 */
class User
{
    use TimestampableEntity;

    /**
     * @ORM\Id @ORM\GeneratedValue @ORM\Column(type="integer")
     * @var int
     */
    protected $id;

    /**
     * @var integer
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="time")
     */
    protected $someTime;

    /**
     * @var Carbon
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="date")
     */
    protected $someDate;

    /**
     * @var Carbon
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    protected $someDateTime;

    /**
     * @var Carbon
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetimetz")
     */
    protected $someDateTimeTz;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getSomeTime()
    {
        return $this->someTime;
    }

    /**
     * @param int $someTime
     */
    public function setSomeTime($someTime)
    {
        $this->someTime = $someTime;
    }

    /**
     * @return Carbon
     */
    public function getSomeDate()
    {
        return $this->someDate;
    }

    /**
     * @param Carbon $someDate
     */
    public function setSomeDate($someDate): void
    {
        $this->someDate = $someDate;
    }

    /**
     * @return Carbon
     */
    public function getSomeDateTime()
    {
        return $this->someDateTime;
    }

    /**
     * @param Carbon $someDateTime
     */
    public function setSomeDateTime($someDateTime): void
    {
        $this->someDateTime = $someDateTime;
    }

    /**
     * @return Carbon
     */
    public function getSomeDateTimeTz()
    {
        return $this->someDateTimeTz;
    }

    /**
     * @param Carbon $someDateTimeTz
     */
    public function setSomeDateTimeTz($someDateTimeTz): void
    {
        $this->someDateTimeTz = $someDateTimeTz;
    }

}