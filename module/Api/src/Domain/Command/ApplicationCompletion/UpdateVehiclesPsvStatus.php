<?php

/**
 * Update VehiclesPsv Status
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Command\ApplicationCompletion;

use Dvsa\Olcs\Transfer\Command\AbstractCommand;

/**
 * Update VehiclesPsv Status
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class UpdateVehiclesPsvStatus extends AbstractCommand
{
    protected $id;

    public function getId()
    {
        return $this->id;
    }
}
