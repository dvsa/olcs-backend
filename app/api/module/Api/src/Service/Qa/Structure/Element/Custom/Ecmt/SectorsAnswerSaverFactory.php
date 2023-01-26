<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Ecmt;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class SectorsAnswerSaverFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return SectorsAnswerSaver
     */
    public function createService(ServiceLocatorInterface $serviceLocator): SectorsAnswerSaver
    {
        return $this->__invoke($serviceLocator, SectorsAnswerSaver::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return SectorsAnswerSaver
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): SectorsAnswerSaver
    {
        return new SectorsAnswerSaver(
            $container->get('RepositoryServiceManager')->get('IrhpApplication'),
            $container->get('QaGenericAnswerFetcher')
        );
    }
}
