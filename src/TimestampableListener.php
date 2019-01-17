<?php

namespace Railroad\Doctrine;

use Gedmo\Timestampable\TimestampableListener as GedmoTimestampableListener;

class TimestampableListener extends GedmoTimestampableListener
{
    protected function getNamespace()
    {
        return __NAMESPACE__;
    }
}