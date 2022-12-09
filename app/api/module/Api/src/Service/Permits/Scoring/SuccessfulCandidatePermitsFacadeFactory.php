<?php

namespace Dvsa\Olcs\Api\Service\Permits\Scoring;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class SuccessfulCandidatePermitsFacadeFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return SuccessfulCandidatePermitsFacade
     */
    public function createService(ServiceLocatorInterface $serviceLocator): SuccessfulCandidatePermitsFacade
    {
        return $this->__invoke($serviceLocator, SuccessfulCandidatePermitsFacade::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return SuccessfulCandidatePermitsFacade
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): SuccessfulCandidatePermitsFacade
    {
        return new SuccessfulCandidatePermitsFacade(
            $container->get('PermitsScoringSuccessfulCandidatePermitsGenerator'),
            $container->get('PermitsScoringSuccessfulCandidatePermitsWriter'),
            $container->get('PermitsScoringSuccessfulCandidatePermitsLogger')
        );
    }
}
