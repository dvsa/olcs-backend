<?php

namespace Dvsa\Olcs\Api\Service\Permits;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class GrantabilityCheckerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return GrantabilityChecker
     */
    public function createService(ServiceLocatorInterface $serviceLocator): GrantabilityChecker
    {
        return $this->__invoke($serviceLocator, GrantabilityChecker::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return GrantabilityChecker
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): GrantabilityChecker
    {
        return new GrantabilityChecker(
            $container->get('PermitsAvailabilityEmissionsCategoriesGrantabilityChecker'),
            $container->get('PermitsAvailabilityCandidatePermitsGrantabilityChecker')
        );
    }
}
