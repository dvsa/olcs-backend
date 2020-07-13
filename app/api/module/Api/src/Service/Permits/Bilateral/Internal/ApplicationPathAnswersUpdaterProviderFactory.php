<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral\Internal;

use Dvsa\Olcs\Api\Entity\Generic\ApplicationPathGroup;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ApplicationPathAnswersUpdaterProviderFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return OtherAnswersUpdater
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $provider = new ApplicationPathAnswersUpdaterProvider();

        $provider->registerUpdater(
            ApplicationPathGroup::BILATERALS_STANDARD_PERMITS_ONLY_ID,
            $serviceLocator->get('PermitsBilateralInternalNullApplicationPathAnswersUpdater')
        );

        $provider->registerUpdater(
            ApplicationPathGroup::BILATERALS_CABOTAGE_PERMITS_ONLY_ID,
            $serviceLocator->get('PermitsBilateralInternalCabotageOnlyApplicationPathAnswersUpdater')
        );

        $provider->registerUpdater(
            ApplicationPathGroup::BILATERALS_STANDARD_AND_CABOTAGE_PERMITS_ID,
            $serviceLocator->get('PermitsBilateralInternalStandardAndCabotageApplicationPathAnswersUpdater')
        );

        $provider->registerUpdater(
            ApplicationPathGroup::BILATERALS_TURKEY_ID,
            $serviceLocator->get('PermitsBilateralInternalTurkeyApplicationPathAnswersUpdater')
        );

        $provider->registerUpdater(
            ApplicationPathGroup::BILATERALS_UKRAINE_ID,
            $serviceLocator->get('PermitsBilateralInternalUkraineApplicationPathAnswersUpdater')
        );

        return $provider;
    }
}
