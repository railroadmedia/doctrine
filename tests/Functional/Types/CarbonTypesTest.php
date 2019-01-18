<?php

use Carbon\Carbon;
use Railroad\Doctrine\Tests\Fixtures\Coupon;
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

    public function test_return_types()
    {
        $coupon = new Coupon();

        $this->entityManager->persist($coupon);
        $this->entityManager->flush();

        $this->entityManager->detach($coupon);
        unset($coupon);

        /**
         * @var $unusedCoupon Coupon
         */
        $unusedCoupon = $this->entityManager->find(Coupon::class, 1);

        // assert null values
        $this->assertEquals(null, $unusedCoupon->getUsedAtTime());
        $this->assertEquals(null, $unusedCoupon->getUsedAtDate());
        $this->assertEquals(null, $unusedCoupon->getUsedAtDateTime());
        $this->assertEquals(null, $unusedCoupon->getUsedAtDateTimeTz());
        // assert Carbon type values
        $this->assertEquals(Carbon::class, get_class($unusedCoupon->getCreatedAt()));
        $this->assertEquals(Carbon::class, get_class($unusedCoupon->getUpdatedAt()));

        $now = Carbon::parse('1970-01-01 00:00:00');

        Carbon::setTestNow($now);

        $unusedCoupon
            ->setUsedAtTime($now)
            ->setUsedAtDate($now)
            ->setUsedAtDateTime($now)
            ->setUsedAtDateTimeTz($now);

        $this->entityManager->flush();

        $this->entityManager->detach($unusedCoupon);
        unset($unusedCoupon);

        /**
         * @var $usedCoupon Coupon
         */
        $usedCoupon = $this->entityManager->find(Coupon::class, 1);

        // assert Carbon type values of previous null values
        $this->assertEquals(Carbon::class, get_class($usedCoupon->getUsedAtTime()));
        $this->assertEquals(Carbon::class, get_class($usedCoupon->getUsedAtDate()));
        $this->assertEquals(Carbon::class, get_class($usedCoupon->getUsedAtDateTime()));
        $this->assertEquals(Carbon::class, get_class($usedCoupon->getUsedAtDateTimeTz()));
    }
}
