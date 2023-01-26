<?php

namespace Dvsa\Olcs\Api\Service\Qa\Strategy;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class BilateralCabotageOnlyFormControlStrategyFactory implements FactoryInterface
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
            'bilateral_cabotage_only',
            $mainServiceLocator->get('QaBilateralCabotageOnlyElementGenerator'),
            $mainServiceLocator->get('QaBilateralCabotageOnlyAnswerSaver'),
            $mainServiceLocator->get('QaGenericAnswerClearer'),
            $mainServiceLocator->get('QaBilateralCabotageQuestionTextGenerator'),
            $mainServiceLocator->get('QaBilateralCabotageOnlyAnswerSummaryProvider')
        );
    }
}
