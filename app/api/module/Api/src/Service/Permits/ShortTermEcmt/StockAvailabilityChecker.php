<?php

namespace Dvsa\Olcs\Api\Service\Permits\ShortTermEcmt;

use Dvsa\Olcs\Api\Entity\System\RefData;

class StockAvailabilityChecker
{
    /** @var EmissionsCategoryAvailabilityChecker */
    private $emissionsCategoryAvailabilityChecker;

    /**
     * Create service instance
     *
     * @param EmissionsCategoryAvailabilityChecker $emissionsCategoryAvailabilityChecker
     *
     * @return StockAvailabilityChecker
     */
    public function __construct(EmissionsCategoryAvailabilityChecker $emissionsCategoryAvailabilityChecker)
    {
        $this->emissionsCategoryAvailabilityChecker = $emissionsCategoryAvailabilityChecker;
    }

    /**
     * Whether there are permits available to apply for within the scope of a specific short term stock
     *
     * @param int $irhpPermitStockId
     *
     * @return bool
     */
    public function hasAvailability($irhpPermitStockId)
    {
        $euro5Available = $this->emissionsCategoryAvailabilityChecker->hasAvailability(
            $irhpPermitStockId,
            RefData::EMISSIONS_CATEGORY_EURO5_REF
        );

        $euro6Available = $this->emissionsCategoryAvailabilityChecker->hasAvailability(
            $irhpPermitStockId,
            RefData::EMISSIONS_CATEGORY_EURO6_REF
        );

        return($euro5Available || $euro6Available);
    }
}
