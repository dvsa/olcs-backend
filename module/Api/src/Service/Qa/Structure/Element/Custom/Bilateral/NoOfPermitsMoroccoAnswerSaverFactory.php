<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Bilateral;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class NoOfPermitsMoroccoAnswerSaverFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return NoOfPermitsMoroccoAnswerSaver
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new NoOfPermitsMoroccoAnswerSaver(
            $serviceLocator->get('QaGenericAnswerFetcher'),
            $serviceLocator->get('PermitsBilateralCommonNoOfPermitsConditionalUpdater')
        );
    }
}
