<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral\Internal;

use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;

class NullApplicationPathAnswersUpdater implements ApplicationPathAnswersUpdaterInterface
{
    /**
     * {@inheritdoc}
     */
    public function update(IrhpPermitApplication $irhpPermitApplication, array $bilateralRequired)
    {
    }
}
