<?php

namespace Dvsa\Olcs\Api\Service\Permits\Scoring;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class SuccessfulCandidatePermitsWriterFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return SuccessfulCandidatePermitsWriter
     */
    public function createService(ServiceLocatorInterface $serviceLocator): SuccessfulCandidatePermitsWriter
    {
        return $this->__invoke($serviceLocator, SuccessfulCandidatePermitsWriter::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return SuccessfulCandidatePermitsWriter
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): SuccessfulCandidatePermitsWriter
    {
        return new SuccessfulCandidatePermitsWriter(
            $container->get('RepositoryServiceManager')->get('IrhpCandidatePermit')
        );
    }
}
