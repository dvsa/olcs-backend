<?php

namespace Dvsa\Olcs\Api\Service\Permits\Availability;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class CandidatePermitsAvailableCountCalculatorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return CandidatePermitsAvailableCountCalculator
     */
    public function createService(ServiceLocatorInterface $serviceLocator): CandidatePermitsAvailableCountCalculator
    {
        return $this->__invoke($serviceLocator, CandidatePermitsAvailableCountCalculator::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return CandidatePermitsAvailableCountCalculator
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): CandidatePermitsAvailableCountCalculator
    {
        $repoServiceManager = $container->get('RepositoryServiceManager');
        return new CandidatePermitsAvailableCountCalculator(
            $repoServiceManager->get('IrhpCandidatePermit'),
            $repoServiceManager->get('IrhpPermit')
        );
    }
}
