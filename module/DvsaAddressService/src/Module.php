<?php

namespace Dvsa\Olcs\DvsaAddressService;

use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Api\Service\Toggle\ToggleService;
use Dvsa\Olcs\DvsaAddressService\Service\DvsaAddressService;

class Module
{
    public function onBootstrap($e)
    {
        $this->overwriteAddressServiceIfToggleEnabled($e);
    }

    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    /**
     * Empty on purpose to defer loading to composer
     */
    public function getAutoloaderConfig()
    {
    }

    /**
     * Overwrite the AddressService alias with the DvsaAddressService if toggle is enabled
     *
     * Can be removed once the old AddressService is decommissioned/removed
     *
     * @param $e
     */
    private function overwriteAddressServiceIfToggleEnabled($e): void
    {
        $toggleService = $e->getApplication()->getServiceManager()->get(ToggleService::class);

        // Overwrite the AddressService alias with the DvsaAddressService if toggle is enabled
        if ($toggleService->isEnabled(FeatureToggle::USE_NEW_ADDRESS_SERVICE)) {
            $serviceManager = $e->getApplication()->getServiceManager();
            $serviceManager->setAlias('AddressService', DvsaAddressService::class);
        }
    }
}
