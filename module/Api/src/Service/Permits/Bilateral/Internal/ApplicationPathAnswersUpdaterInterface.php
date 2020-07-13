<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral\Internal;

use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;

interface ApplicationPathAnswersUpdaterInterface
{
    /**
     * Update the answers that vary between application paths
     *
     * @param IrhpPermitApplication $irhpPermitApplication
     * @param array $bilateralRequired
     */
    public function update(IrhpPermitApplication $irhpPermitApplication, array $bilateralRequired);
}
