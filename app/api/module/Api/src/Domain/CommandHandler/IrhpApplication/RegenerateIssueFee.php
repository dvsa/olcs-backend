<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\IrhpApplication;

use Dvsa\Olcs\Api\Entity\Fee\FeeType;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;

/**
 * Create issue fee (or replace if already present)
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class RegenerateIssueFee extends AbstractRegenerateFee
{
    protected $productReference = FeeType::FEE_TYPE_IRHP_ISSUE_BILATERAL_PRODUCT_REF;

    protected $feeName = 'Issue fee';

    protected function canCreateOrReplaceFee(IrhpApplication $irhpApplication)
    {
        return $irhpApplication->canCreateOrReplaceIssueFee();
    }

    protected function getLatestOutstandingFee(IrhpApplication $irhpApplication)
    {
        return $irhpApplication->getLatestOutstandingIssueFee();
    }
}
