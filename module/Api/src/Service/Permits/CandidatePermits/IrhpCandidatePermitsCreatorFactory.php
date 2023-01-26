<?php

namespace Dvsa\Olcs\Api\Service\Permits\CandidatePermits;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class IrhpCandidatePermitsCreatorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return IrhpCandidatePermitsCreator
     */
    public function createService(ServiceLocatorInterface $serviceLocator): IrhpCandidatePermitsCreator
    {
        return $this->__invoke($serviceLocator, IrhpCandidatePermitsCreator::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return IrhpCandidatePermitsCreator
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): IrhpCandidatePermitsCreator
    {
        return new IrhpCandidatePermitsCreator(
            $container->get('PermitsScoringCandidatePermitsCreator'),
            $container->get('PermitsCandidatePermitsApggCandidatePermitsCreator')
        );
    }
}
