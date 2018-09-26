<?php

/**
 * Irhp window overlap trait
 */

namespace Dvsa\Olcs\Api\Domain\CommandHandler\IrhpPermitWindow;

trait IrhpPermitWindowOverlapTrait
{
    /**
     * Detects overlapping windows for a given stock ID, and new start/end window dates.
     *
     * @param $irhpPermitStock
     * @param $proposedStartDate
     * @param $proposedEndDate
     * @param null $irhpPermitWindow
     * @return int
     */
    public function overlapsExistingWindow($irhpPermitStock, $proposedStartDate, $proposedEndDate, $irhpPermitWindow = null)
    {
        $windowsOverlapping = $this->getRepo()->findOverlappingWindowsByType($irhpPermitStock, $proposedStartDate, $proposedEndDate, $irhpPermitWindow);
        return count($windowsOverlapping);
    }
}
