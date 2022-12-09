<?php

namespace Dvsa\Olcs\Api\Service\Permits\Scoring;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class CandidatePermitsCreatorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return CandidatePermitsCreator
     */
    public function createService(ServiceLocatorInterface $serviceLocator): CandidatePermitsCreator
    {
        return $this->__invoke($serviceLocator, CandidatePermitsCreator::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return CandidatePermitsCreator
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): CandidatePermitsCreator
    {
        $repoServiceManager = $container->get('RepositoryServiceManager');
        return new CandidatePermitsCreator(
            $repoServiceManager->get('IrhpCandidatePermit'),
            $repoServiceManager->get('SystemParameter'),
            $container->get('PermitsScoringIrhpCandidatePermitFactory')
        );
    }
}
