<?php

/**
 * Delete Operating Centre Transport Manager Links
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Domain\Command\OperatingCentre;

use Dvsa\Olcs\Transfer\Command\AbstractCommand;

/**
 * Delete Operating Centre Transport Manager Links
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
final class DeleteTmLinks extends AbstractCommand
{
    protected $operatingCentre;

    /**
     * Gets the value of operatingCentre.
     *
     * @return mixed
     */
    public function getOperatingCentre()
    {
        return $this->operatingCentre;
    }
}
