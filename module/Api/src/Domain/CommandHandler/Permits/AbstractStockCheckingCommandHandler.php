<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;

/**
 * Abstract stock checking handler
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
abstract class AbstractStockCheckingCommandHandler extends AbstractCommandHandler
{
    /**
     * Checks that the specified stockId has at least one free permit available for allocation
     *
     * @param int $stockId
     *
     * @return bool
     */
    protected function passesStockAvailabilityPrerequisite($stockId)
    {
        $combinedRangeSize = $this->getRepo('IrhpPermitRange')->getCombinedRangeSize($stockId);
        if (is_null($combinedRangeSize)) {
            return false;
        }

        $assignedPermits = $this->getRepo('IrhpPermit')->getPermitCount($stockId);
        $permitsAvailable = $combinedRangeSize - $assignedPermits;

        return $permitsAvailable >= 1;
    }
}
