<?php

namespace Dvsa\Olcs\Api\Service\Permits\ShortTermEcmt;

class EmissionsCategoryAvailabilityChecker
{
    /** @var EmissionsCategoryAvailabilityCounter */
    private $emissionsCategoryAvailabilityCounter;

    /**
     * Create service instance
     *
     * @param EmissionsCategoryAvailabilityCounter $emissionsCategoryAvailabilityCounter
     *
     * @return EmissionsCategoryAvailabilityChecker
     */
    public function __construct(EmissionsCategoryAvailabilityCounter $emissionsCategoryAvailabilityCounter)
    {
        $this->emissionsCategoryAvailabilityCounter = $emissionsCategoryAvailabilityCounter;
    }

    /**
     * Whether there are permits available to apply for within the scope of a specific short term stock and emissions
     * category
     *
     * @param int $irhpPermitStockId
     * @param int $emissionsCategoryId
     *
     * @return bool
     */
    public function hasAvailability($irhpPermitStockId, $emissionsCategoryId)
    {
        $availableCount = $this->emissionsCategoryAvailabilityCounter->getCount(
            $irhpPermitStockId,
            $emissionsCategoryId
        );

        return($availableCount > 0);
    }
}
