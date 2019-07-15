<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\QuestionText\Custom\EcmtShortTerm;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class NoOfPermitsGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return NoOfPermitsGenerator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new NoOfPermitsGenerator(
            $serviceLocator->get('QaQuestionTextGenerator'),
            $serviceLocator->get('RepositoryServiceManager')->get('FeeType')
        );
    }
}
