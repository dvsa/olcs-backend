<?php

/**
 * Irhp range overlap trait
 */

namespace Dvsa\Olcs\Api\Domain\CommandHandler\IrhpPermitRange;

trait IrhpPermitRangeOverlapTrait
{
    /**
     * Returns the number of Ranges that overlap with the provided values.
     *
     * @param IrhpPermitStock $irhpPermitStock
     * @param int $proposedStartValue
     * @param int $proposedEndValue
     * @param int $irhpPermitRange
     *
     * @return int
     */
    public function numberOfOverlappingRanges($irhpPermitStock, $proposedStartValue, $proposedEndValue, $irhpPermitRange = null)
    {
        $rangesOverlapping = $this->getRepo()->findOverlappingRangesByType($irhpPermitStock, $proposedStartValue, $proposedEndValue, $irhpPermitRange);
        return count($rangesOverlapping);
    }
}
