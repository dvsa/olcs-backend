<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Ecmt;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class IntJourneysAnswerSaverFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return IntJourneysAnswerSaver
     */
    public function createService(ServiceLocatorInterface $serviceLocator): IntJourneysAnswerSaver
    {
        return $this->__invoke($serviceLocator, IntJourneysAnswerSaver::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return IntJourneysAnswerSaver
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): IntJourneysAnswerSaver
    {
        return new IntJourneysAnswerSaver(
            $container->get('RepositoryServiceManager')->get('IrhpApplication'),
            $container->get('QaGenericAnswerFetcher')
        );
    }
}
