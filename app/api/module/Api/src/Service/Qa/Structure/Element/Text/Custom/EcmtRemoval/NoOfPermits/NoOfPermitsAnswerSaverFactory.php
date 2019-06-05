<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Text\Custom\EcmtRemoval\NoOfPermits;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class NoOfPermitsAnswerSaverFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return NoOfPermitsAnswerSaver
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new NoOfPermitsAnswerSaver(
            $serviceLocator->get('QaGenericAnswerFetcher'),
            $serviceLocator->get('QaEcmtRemovalNoOfPermitsAnswerWriter'),
            $serviceLocator->get('QaEcmtRemovalNoOfPermitsFeeCreator')
        );
    }
}
