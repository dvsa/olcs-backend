<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Ecmt;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class AnnualTripsAbroadAnswerSaverFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return AnnualTripsAbroadAnswerSaver
     */
    public function createService(ServiceLocatorInterface $serviceLocator): AnnualTripsAbroadAnswerSaver
    {
        return $this->__invoke($serviceLocator, AnnualTripsAbroadAnswerSaver::class);
    }

    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return AnnualTripsAbroadAnswerSaver
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): AnnualTripsAbroadAnswerSaver
    {
        return new AnnualTripsAbroadAnswerSaver(
            $container->get('QaBaseAnswerSaver')
        );
    }
}
