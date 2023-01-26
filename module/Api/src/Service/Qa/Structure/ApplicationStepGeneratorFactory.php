<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class ApplicationStepGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return ApplicationStepGenerator
     */
    public function createService(ServiceLocatorInterface $serviceLocator): ApplicationStepGenerator
    {
        return $this->__invoke($serviceLocator, ApplicationStepGenerator::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return ApplicationStepGenerator
     */
public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ApplicationStepGenerator
    {
        return new ApplicationStepGenerator(
            $container->get('FormControlServiceManager'),
            $container->get('QaApplicationStepFactory'),
            $container->get('QaElementGeneratorContextGenerator')
        );
    }
}
