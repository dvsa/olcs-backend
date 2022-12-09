<?php

namespace Dvsa\Olcs\Api\Service\Qa\AnswerSaver;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class ApplicationAnswersClearerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return ApplicationAnswersClearer
     */
    public function createService(ServiceLocatorInterface $serviceLocator): ApplicationAnswersClearer
    {
        return $this->__invoke($serviceLocator, ApplicationAnswersClearer::class);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return ApplicationAnswersClearer
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ApplicationAnswersClearer
    {
        return new ApplicationAnswersClearer(
            $container->get('QaSupplementedApplicationStepsProvider'),
            $container->get('QaContextFactory')
        );
    }
}
