<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral\Internal;

use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Bilateral\EmissionsStandardsAnswerSaver;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class EmissionsStandardsQuestionHandlerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return FixedAnswerQuestionHandler
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new FixedAnswerQuestionHandler(
            $serviceLocator->get('QaGenericAnswerWriter'),
            EmissionsStandardsAnswerSaver::EURO3_OR_EURO4_ANSWER
        );
    }
}
