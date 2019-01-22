<?php

namespace Railroad\Doctrine\Tests\Fixtures;

use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    public function transform(User $user)
    {
        return [
            'id' => $user->getId(),
            'some_time' => $user->getSomeTime()->toDateTimeString(),
            'some_date' => $user->getSomeDate()
                ->toDateTimeString(),
            'some_date_time' => $user->getSomeDateTime()
                ->toDateTimeString(),
            'some_date_time_tz' => $user->getSomeDateTimeTz()
                ->toDateTimeString(),
        ];
    }
}