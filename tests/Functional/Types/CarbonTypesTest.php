<?php

use Carbon\Carbon;
use Railroad\Doctrine\Tests\Fixtures\User;
use Railroad\Doctrine\Tests\TestCase;

class CarbonTypesTest extends TestCase
{
    public function test_all_types_set_properly()
    {
        $now = Carbon::parse('1970-01-01 00:00:00');

        Carbon::setTestNow($now);

        $user = new User();

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->entityManager->detach($user);
        unset($user);

        /**
         * @var $userFromDatabase User
         */
        $userFromDatabase = $this->entityManager->find(User::class, 1);

        $this->assertEquals($now, $userFromDatabase->getSomeTime());
        $this->assertEquals($now, $userFromDatabase->getSomeDate());
        $this->assertEquals($now, $userFromDatabase->getSomeDateTime());
        $this->assertEquals($now, $userFromDatabase->getSomeDateTimeTz());
    }
}
