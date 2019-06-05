<?php

namespace Dvsa\Olcs\Api\Service\Qa\Element;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class QuestionTextGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return QuestionTextGenerator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new QuestionTextGenerator(
            $serviceLocator->get('QaQuestionTextFactory'),
            $serviceLocator->get('QaJsonDecodingFilteredTranslateableTextGenerator')
        );
    }
}
