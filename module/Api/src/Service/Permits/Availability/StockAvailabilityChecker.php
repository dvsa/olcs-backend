<?php

namespace Dvsa\Olcs\Api\Service\Permits\Availability;

class StockAvailabilityChecker
{
    /** @var StockAvailabilityCounter */
    private $stockAvailabilityCounter;

    /**
     * Create service instance
     *
     * @param StockAvailabilityCounter $stockAvailabilityCounter
     *
     * @return StockAvailabilityChecker
     */
    public function __construct(StockAvailabilityCounter $stockAvailabilityCounter)
    {
        $this->stockAvailabilityCounter = $stockAvailabilityCounter;
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
        $count = $this->stockAvailabilityCounter->getCount($irhpPermitStockId);

        return $count > 0;
    }
}
