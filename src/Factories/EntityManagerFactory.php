<?php

namespace Railroad\Doctrine\Factories;

use Illuminate\Contracts\Container\Container;
use Doctrine\Common\EventManager;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;

class EntityManagerFactory
{
    /**
     * @var Container
     */
    protected $container;

    public function __construct(
        Container $container,
    ) {
        $this->container = $container;
    }

    public function createEntityManager($databaseOptions) {

        $ormConfiguration = $this->container->make(Configuration::class);

        $eventManager = $this->container->make(EventManager::class);

        return EntityManager::create(
            $databaseOptions,
            $ormConfiguration,
            $eventManager
        );
    }
}
