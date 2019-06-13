<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Text\Custom\EcmtRemoval\NoOfPermits;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AnswerWriterFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return AnswerWriter
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $repositoryServiceManager = $serviceLocator->get('RepositoryServiceManager');

        return new AnswerWriter(
            $serviceLocator->get('QaEcmtRemovalNoOfPermitsIrhpPermitApplicationFactory'),
            $repositoryServiceManager->get('IrhpPermitApplication'),
            $repositoryServiceManager->get('IrhpPermitWindow'),
            $serviceLocator->get('QaEcmtRemovalNoOfPermitsCurrentDateTimeFactory')
        );
    }
}
