<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Text\Custom\EcmtRemoval\NoOfPermits;

use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitWindow;

class IrhpPermitApplicationFactory
{
    /**
     * Get an IrhpPermitApplication instance tied to the specified application and window
     *
     * @return IrhpPermitApplication
     */
    public function create(IrhpApplication $irhpApplication, IrhpPermitWindow $irhpPermitWindow)
    {
        return IrhpPermitApplication::createNewForIrhpApplication($irhpApplication, $irhpPermitWindow);
    }
}
