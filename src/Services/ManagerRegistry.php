<?php

namespace Railroad\Doctrine\Services;

use Doctrine\Common\Persistence\AbstractManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Contracts\Container\Container;
use Railroad\Doctrine\Factories\EntityManagerFactory;

class ManagerRegistry extends AbstractManagerRegistry
{
    const MANAGER_BINDING_PREFIX = 'doctrine.managers.';
    const DEFAULT_MANAGER_NAME = 'default';

    /**
     * @var Container
     */
    protected $container;

    public function __construct(
        Container $container,
        EntityManagerInterface $defaultEntityManager
    ) {
        $this->container = $container;

        $this->defaultManager = self::DEFAULT_MANAGER_NAME;

        $this->addService($this->defaultManager, $defaultEntityManager);
    }

    /**
     * {@inheritdoc}
     */
    protected function getService($name)
    {
        return $this->container->make($name);
    }

    /**
     * {@inheritdoc}
     */
    protected function resetService($name)
    {
        $this->container->forgetInstance($name);
    }

    /**
     * Gets the laravel container binding name
     *
     * @return string
     */
    public function getManagerBindingName($name)
    {
        return self::MANAGER_BINDING_PREFIX . $name;
    }

    /**
     * Registers an EntityManager instance with both registry and laravel container
     *
     * @return void
     */
    public function addService($name, EntityManagerInterface $service)
    {
        $prefixedName = $this->getManagerBindingName($name);

        $this->container->instance($prefixedName, $service);

        $this->managers[$name] = $prefixedName;
    }
}
