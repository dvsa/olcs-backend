<?php

namespace Dvsa\Olcs\Api\Service\Permits\CandidatePermits;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class ApggCandidatePermitsCreatorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return ApggCandidatePermitsCreator
     */
    public function createService(ServiceLocatorInterface $serviceLocator): ApggCandidatePermitsCreator
    {
        return $this->__invoke($serviceLocator, ApggCandidatePermitsCreator::class);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return ApggCandidatePermitsCreator
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ApggCandidatePermitsCreator
    {
        return new ApggCandidatePermitsCreator(
            $container->get('PermitsCandidatePermitsApggEmissionsCatCandidatePermitsCreator')
        );
    }
}
