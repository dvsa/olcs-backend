<?php

/**
 * RemoveLicenceVehicle.php
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Command\LicenceVehicle;

use Dvsa\Olcs\Api\Domain\Command\AbstractIdOnlyCommand;

/**
 * Class RemoveLicenceVehicle
 *
 * Remove licence vehicle.
 *
 * @package Dvsa\Olcs\Api\Domain\Command\LicenceVehicle
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
final class RemoveLicenceVehicle extends AbstractIdOnlyCommand
{
    protected $licenceVehicles;

    /**
     * @return mixed
     */
    public function getLicenceVehicles()
    {
        return $this->licenceVehicles;
    }
}
