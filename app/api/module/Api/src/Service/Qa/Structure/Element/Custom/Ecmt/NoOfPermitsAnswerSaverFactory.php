<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Ecmt;

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
            $serviceLocator->get('RepositoryServiceManager')->get('IrhpPermitApplication'),
            $serviceLocator->get('QaEcmtNoOfPermitsAnswerFetcher'),
            $serviceLocator->get('QaEcmtConditionalFeeUpdater')
        );
    }
}
