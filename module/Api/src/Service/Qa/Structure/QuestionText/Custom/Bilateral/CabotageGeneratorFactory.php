<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\QuestionText\Custom\Bilateral;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CabotageGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return CabotageGenerator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new CabotageGenerator(
            $serviceLocator->get('QaQuestionTextGenerator')
        );
    }
}
