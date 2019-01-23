<?php

use Carbon\Carbon;
use Railroad\Doctrine\Hydrators\FakeDataHydrator;
use Railroad\Doctrine\Tests\Fixtures\Coupon;
use Railroad\Doctrine\Tests\Fixtures\Resource;
use Railroad\Doctrine\Tests\Fixtures\User;
use Railroad\Doctrine\Tests\TestCase;

class DomainTypesTest extends TestCase
{
    public function test_url_validation_fails()
    {
        $class = new FakeDataHydrator($this->entityManager);
        $resource = new Resource();

        $class->fill($resource);

        $this->entityManager->persist($resource);
        $this->entityManager->flush($resource);

        dd($resource);
    }
}
