<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\CertRoadworthiness;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class MotExpiryDateGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return MotExpiryDateGenerator
     */
    public function createService(ServiceLocatorInterface $serviceLocator): MotExpiryDateGenerator
    {
        return $this->__invoke($serviceLocator, MotExpiryDateGenerator::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return MotExpiryDateGenerator
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): MotExpiryDateGenerator
    {
        return new MotExpiryDateGenerator(
            $container->get('QaCertRoadworthinessMotExpiryDateElementFactory'),
            $container->get('QaCommonDateWithThresholdElementGenerator')
        );
    }
}
