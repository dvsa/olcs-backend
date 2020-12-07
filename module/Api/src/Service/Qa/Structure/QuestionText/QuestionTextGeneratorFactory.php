<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\QuestionText;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

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
