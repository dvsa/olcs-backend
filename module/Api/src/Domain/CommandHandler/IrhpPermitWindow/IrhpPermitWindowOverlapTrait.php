<?php

/**
 * Irhp window overlap trait
 */

namespace Dvsa\Olcs\Api\Domain\CommandHandler\IrhpPermitWindow;

trait IrhpPermitWindowOverlapTrait
{
    /**
     * Returns the number of Windows that overlap with the provided period.
     *
     * @param $irhpPermitStock
     * @param $proposedStartDate
     * @param $proposedEndDate
     * @param null $irhpPermitWindow
     * @return int
     */
    public function numberOfOverlappingWindows($irhpPermitStock, $proposedStartDate, $proposedEndDate, $irhpPermitWindow = null)
    {
        $windowsOverlapping = $this->getRepo()->findOverlappingWindowsByType($irhpPermitStock, $proposedStartDate, $proposedEndDate, $irhpPermitWindow);
        return count($windowsOverlapping);
    }
}
