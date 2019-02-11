<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\IrhpApplication;

use Dvsa\Olcs\Api\Entity\Fee\FeeType;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;

/**
 * Create application fee (or replace if already present)
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class RegenerateApplicationFee extends AbstractRegenerateFee
{
    protected $productReference = FeeType::FEE_TYPE_IRHP_APP_BILATERAL_PRODUCT_REF;

    protected $feeName = 'Application fee';

    protected function canCreateOrReplaceFee(IrhpApplication $irhpApplication)
    {
        return $irhpApplication->canCreateOrReplaceApplicationFee();
    }

    protected function getLatestOutstandingFee(IrhpApplication $irhpApplication)
    {
        return $irhpApplication->getLatestOutstandingApplicationFee();
    }
}
