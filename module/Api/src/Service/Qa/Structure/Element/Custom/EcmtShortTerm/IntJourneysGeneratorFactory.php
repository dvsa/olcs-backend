<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\EcmtShortTerm;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

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
            $serviceLocator->get('QaEcmtShortTermIntJourneysElementFactory'),
            $serviceLocator->get('QaRadioElementGenerator')
        );
    }
}
