<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Ecmt;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class IntJourneysGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return IntJourneysGenerator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new IntJourneysGenerator(
            $serviceLocator->get('QaEcmtIntJourneysElementFactory'),
            $serviceLocator->get('QaRadioElementGenerator')
        );
    }
}
