<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\QuestionText\Custom\Common;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CertificatesGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return CertificatesGenerator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new CertificatesGenerator(
            $serviceLocator->get('QaQuestionTextGenerator')
        );
    }
}
