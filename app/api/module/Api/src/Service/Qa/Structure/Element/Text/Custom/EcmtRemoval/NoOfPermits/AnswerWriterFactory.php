<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Text\Custom\EcmtRemoval\NoOfPermits;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

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
        return new AnswerWriter(
            $serviceLocator->get('RepositoryServiceManager')->get('IrhpPermitApplication')
        );
    }
}
