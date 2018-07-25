<?php

/**
 * With Contact Details Factory
 */
namespace Dvsa\Olcs\Api\Domain\QueryPartial;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * With Contact Details Factory
 */
class WithContactDetailsFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, self::class);
    }

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new WithContactDetails(
            $container->get('doctrine.entitymanager.orm_default'),
            $container->get('with'),
            $container->get('withRefdata')
        );
    }
}
