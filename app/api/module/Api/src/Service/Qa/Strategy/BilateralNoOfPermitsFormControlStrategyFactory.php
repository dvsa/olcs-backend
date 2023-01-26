<?php

namespace Dvsa\Olcs\Api\Service\Qa\Strategy;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class BilateralNoOfPermitsFormControlStrategyFactory implements FactoryInterface
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
            'bilateral_number_of_permits',
            $mainServiceLocator->get('QaBilateralNoOfPermitsElementGenerator'),
            $mainServiceLocator->get('QaBilateralNoOfPermitsAnswerSaver'),
            $mainServiceLocator->get('QaBilateralNoOfPermitsAnswerClearer'),
            $mainServiceLocator->get('QaQuestionTextGenerator'),
            $mainServiceLocator->get('QaBilateralNoOfPermitsAnswerSummaryProvider')
        );
    }
}
