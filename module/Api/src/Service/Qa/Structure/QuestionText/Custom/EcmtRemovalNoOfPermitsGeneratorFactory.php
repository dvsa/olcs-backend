<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\QuestionText\Custom;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class EcmtRemovalNoOfPermitsGeneratorFactory implements FactoryInterface
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
        return new EcmtRemovalNoOfPermitsGenerator(
            $serviceLocator->get('QaQuestionTextGenerator'),
            $serviceLocator->get('RepositoryServiceManager')->get('FeeType')
        );
    }
}
