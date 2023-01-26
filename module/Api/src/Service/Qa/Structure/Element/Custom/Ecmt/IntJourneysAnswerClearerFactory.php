<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Ecmt;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class IntJourneysAnswerClearerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return IntJourneysAnswerClearer
     */
    public function createService(ServiceLocatorInterface $serviceLocator): IntJourneysAnswerClearer
    {
        return $this->__invoke($serviceLocator, IntJourneysAnswerClearer::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return IntJourneysAnswerClearer
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): IntJourneysAnswerClearer
    {
        return new IntJourneysAnswerClearer(
            $container->get('RepositoryServiceManager')->get('IrhpApplication')
        );
    }
}
