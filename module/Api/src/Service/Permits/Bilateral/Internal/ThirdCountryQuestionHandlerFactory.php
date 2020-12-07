<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral\Internal;

use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Bilateral\ThirdCountryAnswerSaver;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class ThirdCountryQuestionHandlerFactory implements FactoryInterface
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
            ThirdCountryAnswerSaver::YES_ANSWER
        );
    }
}
