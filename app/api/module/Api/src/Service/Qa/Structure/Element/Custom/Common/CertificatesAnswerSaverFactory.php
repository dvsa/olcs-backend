<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Common;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class CertificatesAnswerSaverFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return CertificatesAnswerSaver
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new CertificatesAnswerSaver(
            $serviceLocator->get('QaBaseAnswerSaver')
        );
    }
}
