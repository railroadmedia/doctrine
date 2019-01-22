<?php

namespace Railroad\Doctrine\Hydrators;

use Carbon\Carbon;
use Closure;
use DateTime;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Faker\ORM\Doctrine\ColumnTypeGuesser;

class ColumnTypeGuesserCarbon extends ColumnTypeGuesser
{
    /**
     * @param $fieldName
     * @param ClassMetadata $class
     * @return Carbon|Closure|null
     */
    public function guessFormat($fieldName, ClassMetadata $class)
    {
        $value = parent::guessFormat($fieldName, $class);

        if (is_callable($value) && $value() instanceof DateTime) {
            return Carbon::instance($value());
        }

        // for some reason the built in faker type guesser doesn't work for datetimetz, my guess its just outdated
        // - Caleb Jan 2019
        if ($class->getTypeOfField($fieldName) == 'datetimetz') {
            return Carbon::instance($this->generator->dateTime);
        }

        return $value;
    }
}