<?php

use Railroad\Doctrine\Hydrators\FakeDataHydrator;
use Railroad\Doctrine\Tests\Fixtures\User;
use Railroad\Doctrine\Tests\Fixtures\UserTransformer;
use Railroad\Doctrine\Tests\TestCase;

class FakeDataHydratorTest extends TestCase
{
    public function test_hydrate_and_attribute_array()
    {
        $class = new FakeDataHydrator($this->entityManager);

        $user = new User();

        $class->fill($user);

        $this->assertNotEmpty($user->getSomeTime());
        $this->assertNotEmpty($user->getSomeDate());
        $this->assertNotEmpty($user->getSomeDateTime());
        $this->assertNotEmpty($user->getSomeDateTimeTz());

        $userAttributes = $class->getAttributeArray($user, new UserTransformer());

        $this->assertEquals($user->getSomeTime(), $userAttributes['some_time']);
        $this->assertEquals($user->getSomeDate(), $userAttributes['some_date']);
        $this->assertEquals($user->getSomeDateTime(), $userAttributes['some_date_time']);
        $this->assertEquals($user->getSomeDateTimeTz(), $userAttributes['some_date_time_tz']);
    }
    
    public function test_attribute_array_just_array()
    {
        $class = new FakeDataHydrator($this->entityManager);

        $userAttributes = $class->getAttributeArray(User::class, new UserTransformer());

        $this->assertNotEmpty($userAttributes['some_time']);
        $this->assertNotEmpty($userAttributes['some_date']);
        $this->assertNotEmpty($userAttributes['some_date_time']);
        $this->assertNotEmpty($userAttributes['some_date_time_tz']);
    }
}
