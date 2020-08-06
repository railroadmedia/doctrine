<?php

use Railroad\Doctrine\Hydrators\FakeDataHydrator;
use Railroad\Doctrine\Serializers\BasicEntitySerializer;
use Railroad\Doctrine\Tests\Fixtures\User;
use Railroad\Doctrine\Tests\Fixtures\UserTransformer;
use Railroad\Doctrine\Tests\TestCase;

class BasicEntitySerializerTest extends TestCase
{
    public function test_hydrate_and_attribute_array()
    {
        $hydrator = new FakeDataHydrator($this->entityManager);

        $user = new User();

        $hydrator->fill($user);

        $serializer = new BasicEntitySerializer();

        $array = $serializer->serializeToUnderScores($user, $this->entityManager->getClassMetadata(User::class));

        $this->assertIsArray($array);
    }
}
