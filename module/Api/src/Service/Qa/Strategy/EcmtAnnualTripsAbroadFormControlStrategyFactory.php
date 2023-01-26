<?php

namespace Dvsa\Olcs\Api\Service\Qa\Strategy;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class EcmtAnnualTripsAbroadFormControlStrategyFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return BaseFormControlStrategy
     */
    public function createService(ServiceLocatorInterface $serviceLocator): BaseFormControlStrategy
    {
        return $this->__invoke($serviceLocator, BaseFormControlStrategy::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return BaseFormControlStrategy
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): BaseFormControlStrategy
    {
        $mainServiceLocator = $container->getServiceLocator();
        return new BaseFormControlStrategy(
            'ecmt_st_annual_trips_abroad',
            $mainServiceLocator->get('QaEcmtAnnualTripsAbroadElementGenerator'),
            $mainServiceLocator->get('QaEcmtAnnualTripsAbroadAnswerSaver'),
            $mainServiceLocator->get('QaGenericAnswerClearer'),
            $mainServiceLocator->get('QaQuestionTextGenerator'),
            $mainServiceLocator->get('QaGenericAnswerSummaryProvider')
        );
    }
}
