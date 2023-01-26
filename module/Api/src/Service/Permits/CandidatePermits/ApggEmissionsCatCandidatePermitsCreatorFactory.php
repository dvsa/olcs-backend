<?php

namespace Dvsa\Olcs\Api\Service\Permits\CandidatePermits;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class ApggEmissionsCatCandidatePermitsCreatorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return ApggEmissionsCatCandidatePermitsCreator
     */
    public function createService(ServiceLocatorInterface $serviceLocator): ApggEmissionsCatCandidatePermitsCreator
    {
        return $this->__invoke($serviceLocator, ApggEmissionsCatCandidatePermitsCreator::class);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return ApggEmissionsCatCandidatePermitsCreator
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ApggEmissionsCatCandidatePermitsCreator
    {
        return new ApggEmissionsCatCandidatePermitsCreator(
            $container->get('PermitsCandidatePermitsApggCandidatePermitFactory'),
            $container->get('RepositoryServiceManager')->get('IrhpCandidatePermit'),
            $container->get('PermitsAllocateEmissionsStandardCriteriaFactory')
        );
    }
}
