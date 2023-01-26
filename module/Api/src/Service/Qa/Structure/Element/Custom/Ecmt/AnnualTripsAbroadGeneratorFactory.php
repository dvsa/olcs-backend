<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Ecmt;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class AnnualTripsAbroadGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return AnnualTripsAbroadGenerator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this->__invoke($serviceLocator, AnnualTripsAbroadAnswerSaver::class);
    }
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new AnnualTripsAbroadGenerator(
            $container->get('QaEcmtAnnualTripsAbroadElementFactory'),
            $container->get('QaTextElementGenerator')
        );
    }
}
