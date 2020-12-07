<?php

namespace Dvsa\Olcs\Api\Service\Permits\AnswersSummary;

use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class AnswersSummaryGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return AnswersSummaryGenerator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $answersSummaryGenerator = new AnswersSummaryGenerator(
            $serviceLocator->get('PermitsAnswersSummaryFactory'),
            $serviceLocator->get('PermitsHeaderAnswersSummaryRowsAdder'),
            $serviceLocator->get('QaAnswersSummaryRowsAdder')
        );

        $answersSummaryGenerator->registerCustomRowsAdder(
            IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL,
            $serviceLocator->get('PermitsBilateralAnswersSummaryRowsAdder')
        );

        $answersSummaryGenerator->registerCustomRowsAdder(
            IrhpPermitType::IRHP_PERMIT_TYPE_ID_MULTILATERAL,
            $serviceLocator->get('PermitsMultilateralAnswersSummaryRowsAdder')
        );

        return $answersSummaryGenerator;
    }
}
