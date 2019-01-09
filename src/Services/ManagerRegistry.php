<?php

namespace Railroad\Doctrine\Services;

use Doctrine\Common\Persistence\AbstractManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
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
        parent::__construct('ORM', [], [], null, self::DEFAULT_MANAGER_NAME, 'Doctrine\ORM\Proxy\Proxy');

        $this->container = $container;

        $this->addService(self::DEFAULT_MANAGER_NAME, $defaultEntityManager);
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
     * Resolves a registered namespace alias to the full namespace.
     *
     * This method looks for the alias in all registered entity managers.
     *
     * @see Configuration::getEntityNamespace
     *
     * @param string $alias The alias
     *
     * @return string The full namespace
     */
    public function getAliasNamespace($alias)
    {
        foreach (array_keys($this->getManagers()) as $name) {
            try {
                return $this->getManager($name)->getConfiguration()->getEntityNamespace($alias);
            } catch (ORMException $e) {
            }
        }

        throw ORMException::unknownEntityNamespace($alias);
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
