<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral\Internal;

use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitWindow;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;

class IrhpPermitApplicationFactory
{
    /**
     * Create instance of IrhpPermitApplication
     *
     * @param IrhpApplication $irhpApplication
     * @param IrhpPermitWindow $irhpPermitWindow
     *
     * @throws IrhpPermitApplication
     */
    public function create(IrhpApplication $irhpApplication, IrhpPermitWindow $irhpPermitWindow)
    {
        return IrhpPermitApplication::createNewForIrhpApplication($irhpApplication, $irhpPermitWindow);
    }
}
