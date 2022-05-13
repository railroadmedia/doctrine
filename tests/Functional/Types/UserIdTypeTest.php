<?php

use Carbon\Carbon;
use Doctrine\ORM\Query\Expr\Join;
use Railroad\Doctrine\Tests\TestCase;
use Illuminate\Database\DatabaseManager;
use Railroad\Doctrine\Contracts\UserProviderInterface;
use Railroad\Doctrine\Tests\Fixtures\Address;
use Railroad\Doctrine\Tests\Fixtures\Contact;
use Railroad\Doctrine\Tests\Fixtures\UserEntity;
use Railroad\Doctrine\Tests\Fixtures\UserProvider;

class UserIdTypeTest extends TestCase
{
    protected $databaseManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->databaseManager = $this->app->make(DatabaseManager::class);

        app()->instance(UserProviderInterface::class, new UserProvider());
    }

    public function test_persist()
    {
        $userProvider = app()->make(UserProviderInterface::class);

        $userId = rand();

        /**
         * @var $user UserEntity
         */
        $user = $userProvider->getUserById($userId);

        $address = new Address();

        $address->setUserId($user);

        $this->entityManager->persist($address);
        $this->entityManager->flush();

        $this->entityManager->detach($address);
        unset($address);

        $this->assertDatabaseHas(
            'addresses',
            [
                'user_id' => $userId
            ]
        );
    }

    public function test_fetch()
    {
        $userId = rand();

        $addressId = $this->databaseManager
            ->table('addresses')
            ->insertGetId(['user_id' => $userId]);

        $address = $this->entityManager->find(Address::class, $addressId);

        $this->assertEquals(Address::class, get_class($address));

        /**
         * @var $address Address
         */

        $user = $address->getUserId();

        $this->assertEquals(UserEntity::class, get_class($user));

        /**
         * @var $user UserEntity
         */

        $this->assertEquals($user->getId(), $userId);
    }

    public function test_where()
    {
        $userProvider = app()->make(UserProviderInterface::class);

        // seed two addresses
        $userIdOne = rand();

        $addressIdOne = $this->databaseManager
            ->table('addresses')
            ->insertGetId(['user_id' => $userIdOne]);

        $userIdTwo = rand();

        $addressIdTwo = $this->databaseManager
            ->table('addresses')
            ->insertGetId(['user_id' => $userIdTwo]);

        /**
         * @var $addressRepository Doctrine\ORM\EntityRepository
         */
        $addressRepository = $this->entityManager
                                ->getRepository(Address::class);
        /**
         * @var $user UserEntity
         */
        $userOne = $userProvider->getUserById($userIdOne);

        $addressOne = $addressRepository
                        ->findOneByUserId($userOne); // default where, single result

        // assert fetched result is as expected
        $this->assertEquals(Address::class, get_class($addressOne));

        /**
         * @var $addressOne Address
         */


        $addressOneUser = $addressOne->getUserId();

        $this->assertEquals(UserEntity::class, get_class($addressOneUser));

        /**
         * @var $addressOneUser UserEntity
         */

        $this->assertEquals($addressOneUser->getId(), $userOne->getId());

        $this->assertNotEquals($addressOneUser->getId(), $userIdTwo);
    }

    public function test_dql()
    {
        $userProvider = app()->make(UserProviderInterface::class);

        // seed two addresses
        $userIdOne = rand();

        $addressIdOne = $this->databaseManager
            ->table('addresses')
            ->insertGetId(['user_id' => $userIdOne]);

        $userIdTwo = rand();

        $addressIdTwo = $this->databaseManager
            ->table('addresses')
            ->insertGetId(['user_id' => $userIdTwo]);

        $userOne = $userProvider->getUserById($userIdOne);

        /**
         * @var $qb \Doctrine\ORM\QueryBuilder
         */
        $qb = $this->entityManager
                    ->getRepository(Address::class)
                    ->createQueryBuilder('a');

        $qb
            ->select('a')
            ->where($qb->expr()->in('a.userId', ':user')) // where in
            ->setParameter('user', $userOne);

        $addressOne = $qb->getQuery()->getSingleResult();

        // assert fetched result is as expected
        $this->assertEquals(Address::class, get_class($addressOne));

        /**
         * @var $addressOne Address
         */


        $addressOneUser = $addressOne->getUserId();

        $this->assertEquals(UserEntity::class, get_class($addressOneUser));

        /**
         * @var $addressOneUser UserEntity
         */

        $this->assertEquals($addressOneUser->getId(), $userOne->getId());

        $this->assertNotEquals($addressOneUser->getId(), $userIdTwo);
    }

    public function test_join()
    {
        $userProvider = app()->make(UserProviderInterface::class);

        // seed two addresses & two contacts
        $userOneId = rand();

        $addressOneId = $this->databaseManager
            ->table('addresses')
            ->insertGetId(['user_id' => $userOneId]);

        $contactOneId = $this->databaseManager
            ->table('contacts')
            ->insertGetId(['user_id' => $userOneId]);

        $userTwoId = rand();

        $addressTwoId = $this->databaseManager
            ->table('addresses')
            ->insertGetId(['user_id' => $userTwoId]);

        $contactTwoId = $this->databaseManager
            ->table('contacts')
            ->insertGetId(['user_id' => $userTwoId]);

        /**
         * @var $qb \Doctrine\ORM\QueryBuilder
         */
        $qb = $this->entityManager
                    ->getRepository(Address::class)
                    ->createQueryBuilder('a');

        // join by user_id column, filter by contact id one
        $qb
            ->select(['a'])
            ->leftJoin(
                Contact::class,
                'c',
                Join::WITH,
                $qb->expr()->eq('a.userId', 'c.userId')
            )
            ->where($qb->expr()->eq('c.id', ':contact'))
            ->setParameter('contact', $contactOneId);

        $addressOne = $qb->getQuery()->getSingleResult();

        // assert fetched result is as expected
        $this->assertEquals(Address::class, get_class($addressOne));

        /**
         * @var $addressOne Address
         */

        $this->assertEquals($addressOne->getId(), $addressOneId);
    }

    public function test_persist_null()
    {
        $address = new Address();

        $this->entityManager->persist($address);
        $this->entityManager->flush();

        $addressId = $address->getId();

        $this->entityManager->detach($address);
        unset($address);

        $this->assertDatabaseHas(
            'addresses',
            [
                'id' => $addressId,
                'user_id' => null
            ]
        );
    }

    public function test_fetch_null()
    {
        $addressId = $this->databaseManager
            ->table('addresses')
            ->insertGetId(['user_id' => null]);

        $address = $this->entityManager->find(Address::class, $addressId);

        $this->assertEquals(Address::class, get_class($address));

        /**
         * @var $address Address
         */

        $this->assertNull($address->getUserId());
    }
}
