<?php

namespace Dvsa\Olcs\Api\Service\Permits\AnswersSummary;

use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class IpaAnswersSummaryGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return IpaAnswersSummaryGenerator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $answersSummaryGenerator = new IpaAnswersSummaryGenerator(
            $serviceLocator->get('PermitsAnswersSummaryFactory'),
            $serviceLocator->get('QaAnswersSummaryRowsAdder')
        );

        $answersSummaryGenerator->registerCustomRowsAdder(
            IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL,
            $serviceLocator->get('PermitsBilateralIpaAnswersSummaryRowsAdder')
        );

        return $answersSummaryGenerator;
    }
}
