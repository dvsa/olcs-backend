<?php

namespace Dvsa\Olcs\Api\Service\Qa\AnswersSummary;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AnswersSummaryRowGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return AnswersSummaryRowGenerator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new AnswersSummaryRowGenerator(
            $serviceLocator->get('PermitsAnswersSummaryRowFactory'),
            $serviceLocator->get('ViewRenderer'),
            $serviceLocator->get('QaQuestionTextGeneratorContextFactory')
        );
    }
}
