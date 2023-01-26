<?php

namespace Dvsa\Olcs\Api\Service\Qa\Strategy;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class AbstractEcmtNoOfPermitsFormControlStrategyFactory implements FactoryInterface
{
    protected $frontendComponent = 'changeMe';

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
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return BaseFormControlStrategy
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): BaseFormControlStrategy
    {
        $mainServiceLocator = $container->getServiceLocator();
        return new BaseFormControlStrategy(
            $this->frontendComponent,
            $mainServiceLocator->get('QaEcmtNoOfPermitsElementGenerator'),
            $mainServiceLocator->get('QaEcmtNoOfPermitsAnswerSaver'),
            $mainServiceLocator->get('QaEcmtNoOfPermitsAnswerClearer'),
            $mainServiceLocator->get('QaQuestionTextGenerator'),
            $mainServiceLocator->get('QaEcmtNoOfPermitsAnswerSummaryProvider')
        );
    }
}
