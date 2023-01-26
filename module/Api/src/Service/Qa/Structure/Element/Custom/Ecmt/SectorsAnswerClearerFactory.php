<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Ecmt;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class SectorsAnswerClearerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return SectorsAnswerClearer
     */
    public function createService(ServiceLocatorInterface $serviceLocator): SectorsAnswerClearer
    {
        return $this->__invoke($serviceLocator, SectorsAnswerClearer::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return SectorsAnswerClearer
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): SectorsAnswerClearer
    {
        return new SectorsAnswerClearer(
            $container->get('RepositoryServiceManager')->get('IrhpApplication')
        );
    }
}
