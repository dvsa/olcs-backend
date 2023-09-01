<?php

namespace Dvsa\Olcs\Api\Service\Qa\Strategy;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

class BilateralCabotageOnlyFormControlStrategyFactory implements FactoryInterface
{
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
        return new BaseFormControlStrategy(
            'bilateral_cabotage_only',
            $container->get('QaBilateralCabotageOnlyElementGenerator'),
            $container->get('QaBilateralCabotageOnlyAnswerSaver'),
            $container->get('QaGenericAnswerClearer'),
            $container->get('QaBilateralCabotageQuestionTextGenerator'),
            $container->get('QaBilateralCabotageOnlyAnswerSummaryProvider')
        );
    }
}
