<?php

namespace Railroad\Doctrine\Providers;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\Annotations\IndexedReader;
use Doctrine\Common\Annotations\PsrCachedReader;
use Doctrine\Common\Cache\Psr6\DoctrineProvider;
use Doctrine\Common\Cache\RedisCache;
use Doctrine\Common\EventManager;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Cache\DefaultCacheFactory;
use Doctrine\ORM\Cache\RegionsConfiguration;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;
use Doctrine\Persistence\Mapping\Driver\MappingDriverChain;
use Gedmo\DoctrineExtensions;
use Gedmo\Sortable\SortableListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Railroad\Doctrine\TimestampableListener;
use Railroad\Doctrine\Types\Carbon\CarbonDateTimeTimezoneType;
use Railroad\Doctrine\Types\Carbon\CarbonDateTimeType;
use Railroad\Doctrine\Types\Carbon\CarbonDateType;
use Railroad\Doctrine\Types\Carbon\CarbonTimeType;
use Railroad\Doctrine\Types\Domain\GenderType;
use Railroad\Doctrine\Types\Domain\PhoneNumberType;
use Railroad\Doctrine\Types\Domain\TimezoneType;
use Railroad\Doctrine\Types\Domain\UrlType;
use Railroad\Doctrine\Types\Domain\UserIdType;
use Redis;
use Symfony\Component\Cache\Adapter\RedisAdapter;

class DoctrineServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     * @throws \Doctrine\DBAL\DBALException
     */
    public function boot()
    {
        parent::boot();
    }

    /**
     * Register the application services.
     *
     * @return void
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function register()
    {
        // use Carbon for all date types
        Type::overrideType('datetime', CarbonDateTimeType::class);
        Type::overrideType('datetimetz', CarbonDateTimeTimezoneType::class);
        Type::overrideType('date', CarbonDateType::class);
        Type::overrideType('time', CarbonTimeType::class);

        !Type::hasType('url') ? Type::addType('url', UrlType::class) : null;
        !Type::hasType('phone_number') ? Type::addType('phone_number', PhoneNumberType::class) : null;
        !Type::hasType('timezone') ? Type::addType('timezone', TimezoneType::class) : null;
        !Type::hasType('gender') ? Type::addType('gender', GenderType::class) : null;
        !Type::hasType(UserIdType::USER_ID_TYPE) ? Type::addType(UserIdType::USER_ID_TYPE, UserIdType::class) : null;

        // set proxy dir to temp folder on server
        $proxyDir = sys_get_temp_dir();

        // setup redis
        $redis = new Redis();
        $redis->connect(
            config('doctrine.redis_host'),
            config('doctrine.redis_port')
        );
        $redisCacheAdapter = new RedisAdapter($redis);
        $doctrineRedisCache = DoctrineProvider::wrap($redisCacheAdapter);

        // redis cache instance is referenced in laravel container to be reused when needed
        AnnotationRegistry::registerLoader('class_exists');

        $annotationReader = new IndexedReader(new AnnotationReader());

        $cachedAnnotationReader = new PsrCachedReader(
            $annotationReader,
            $redisCacheAdapter,
            env('APP_DEBUG', false)
        );

        $driverChain = new MappingDriverChain();

        DoctrineExtensions::registerAbstractMappingIntoDriverChainORM(
            $driverChain,
            $cachedAnnotationReader
        );

        foreach (config('doctrine.entities') as $driverConfig) {
            $annotationDriver = new AnnotationDriver(
                $cachedAnnotationReader, $driverConfig['path']
            );

            $driverChain->addDriver(
                $annotationDriver,
                $driverConfig['namespace']
            );
        }

        // driver chain instance is referenced in laravel container to be reused when needed
        app()->instance(MappingDriverChain::class, $driverChain);

        $timestampableListener = new TimestampableListener();
        $timestampableListener->setAnnotationReader($cachedAnnotationReader);

        $sortableListener = new SortableListener();
        $sortableListener->setAnnotationReader($cachedAnnotationReader);

        $eventManager = new EventManager();
        $eventManager->addEventSubscriber($timestampableListener);
        $eventManager->addEventSubscriber($sortableListener);

        // event manager instance is referenced in laravel container to be reused when needed
        app()->instance(EventManager::class, $eventManager);

        $ormConfiguration = new Configuration();
        $ormConfiguration->setMetadataCache($redisCacheAdapter);
        $ormConfiguration->setQueryCache($redisCacheAdapter);
        $ormConfiguration->setResultCache($redisCacheAdapter);
        $factory = new DefaultCacheFactory(new RegionsConfiguration(), $redisCacheAdapter);
        $ormConfiguration->setSecondLevelCacheEnabled();
        $ormConfiguration->getSecondLevelCacheConfiguration()->setCacheFactory($factory);

        $ormConfiguration->setProxyDir($proxyDir);
        $ormConfiguration->setProxyNamespace('DoctrineProxies');
        $ormConfiguration->setAutoGenerateProxyClasses(
            config('doctrine.development_mode')
        );
        $ormConfiguration->setMetadataDriverImpl($driverChain);
        $ormConfiguration->setNamingStrategy(
            new UnderscoreNamingStrategy(CASE_LOWER)
        );

        $ormConfiguration->addCustomStringFunction('MATCH_AGAINST','Railroad\\Doctrine\\Extensions\\Doctrine\\MatchAgainst');
        $ormConfiguration->addCustomStringFunction('UNIX_TIMESTAMP','Railroad\\Doctrine\\Extensions\\Doctrine\\UnixTimestamp');

        // orm configuration instance is referenced in laravel container to be reused when needed
        app()->instance(Configuration::class, $ormConfiguration);

        if (config('doctrine.database_in_memory') !== true) {
            $databaseOptions = [
                'driver' => config('doctrine.database_driver'),
                'dbname' => config('doctrine.database_name'),
                'user' => config('doctrine.database_user'),
                'password' => config('doctrine.database_password'),
                'host' => config('doctrine.database_host'),
            ];
        } else {
            $databaseOptions = [
                'driver' => config('doctrine.database_driver'),
                'user' => config('doctrine.database_user'),
                'password' => config('doctrine.database_password'),
                'memory' => true,
            ];
        }

        // register the default entity manager
        $entityManager = EntityManager::create(
            $databaseOptions,
            $ormConfiguration,
            $eventManager
        );

        $entityManager
            ->getConnection()
            ->getDatabasePlatform()
            ->registerDoctrineTypeMapping(
                UserIdType::USER_ID_TYPE,
                UserIdType::USER_ID_TYPE
            );

        // register the entity manager as a singleton
        app()->instance(EntityManager::class, $entityManager);
        app()->instance(EntityManagerInterface::class, $entityManager);
    }
}
