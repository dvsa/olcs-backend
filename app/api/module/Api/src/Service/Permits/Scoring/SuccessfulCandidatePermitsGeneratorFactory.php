<?php

namespace Dvsa\Olcs\Api\Service\Permits\Scoring;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class SuccessfulCandidatePermitsGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return SuccessfulCandidatePermitsGenerator
     */
    public function createService(ServiceLocatorInterface $serviceLocator): SuccessfulCandidatePermitsGenerator
    {
        return $this->__invoke($serviceLocator, SuccessfulCandidatePermitsGenerator::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return SuccessfulCandidatePermitsGenerator
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): SuccessfulCandidatePermitsGenerator
    {
        return new SuccessfulCandidatePermitsGenerator(
            $container->get('PermitsScoringEmissionsCategoryAvailabilityCounter')
        );
    }
}
