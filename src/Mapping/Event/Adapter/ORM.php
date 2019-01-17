<?php

namespace Railroad\Doctrine\Mapping\Event\Adapter;

use Carbon\Carbon;
use DateTimeImmutable;
use Exception;
use Gedmo\Mapping\Event\Adapter\ORM as BaseAdapterORM;
use Gedmo\Timestampable\Mapping\Event\TimestampableAdapter;

final class ORM extends BaseAdapterORM implements TimestampableAdapter
{
    /**
     * @param object $meta
     * @param string $field
     * @return Carbon|DateTimeImmutable|int|mixed
     * @throws Exception
     */
    public function getDateValue($meta, $field)
    {
        $mapping = $meta->getFieldMapping($field);

        if (isset($mapping['type']) && $mapping['type'] === 'integer') {
            return Carbon::now()->timestamp;
        }

        if (isset($mapping['type']) && in_array(
                $mapping['type'],
                ['date_immutable', 'time_immutable', 'datetime_immutable', 'datetimetz_immutable'],
                true
            )) {

            return new DateTimeImmutable();
        }

        return Carbon::now();
    }
}
