<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral\Internal;

use Dvsa\Olcs\Api\Entity\Generic\Answer;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class CabotageOnlyQuestionHandlerFactory implements FactoryInterface
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
            Answer::BILATERAL_CABOTAGE_ONLY
        );
    }
}
