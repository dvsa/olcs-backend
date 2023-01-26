<?php

/**
 * With Contact Details Factory
 */
namespace Dvsa\Olcs\Api\Domain\QueryPartial;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

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
    public function createService(ServiceLocatorInterface $serviceLocator): WithContactDetails
    {
        return $this->__invoke($serviceLocator, WithContactDetails::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return WithContactDetails
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): WithContactDetails
    {
        return new WithContactDetails(
            $container->getServiceLocator()->get('doctrine.entitymanager.orm_default'),
            $container->get('with'),
            $container->get('withRefdata')
        );
    }
}
