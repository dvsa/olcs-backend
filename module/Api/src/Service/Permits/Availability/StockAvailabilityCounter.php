<?php

namespace Dvsa\Olcs\Api\Service\Permits\Availability;

use Dvsa\Olcs\Api\Entity\System\RefData;

class StockAvailabilityCounter
{
    /** @var EmissionsCategoryAvailabilityCounter */
    private $emissionsCategoryAvailabilityCounter;

    /**
     * Create service instance
     *
     * @param EmissionsCategoryAvailabilityCounter $emissionsCategoryAvailabilityCounter
     *
     * @return StockAvailabilityCounter
     */
    public function __construct(EmissionsCategoryAvailabilityCounter $emissionsCategoryAvailabilityCounter)
    {
        $this->emissionsCategoryAvailabilityCounter = $emissionsCategoryAvailabilityCounter;
    }

    /**
     * Get the count of permits availabile to apply for within the scope of a specific stock
     *
     * @param int $irhpPermitStockId
     *
     * @return int
     */
    public function getCount($irhpPermitStockId)
    {
        $euro5Count = $this->emissionsCategoryAvailabilityCounter->getCount(
            $irhpPermitStockId,
            RefData::EMISSIONS_CATEGORY_EURO5_REF
        );

        $euro6Count = $this->emissionsCategoryAvailabilityCounter->getCount(
            $irhpPermitStockId,
            RefData::EMISSIONS_CATEGORY_EURO6_REF
        );

        return $euro5Count + $euro6Count;
    }
}
