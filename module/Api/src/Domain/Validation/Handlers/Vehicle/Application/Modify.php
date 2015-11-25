<?php

/**
 * Modify
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Validation\Handlers\Vehicle\Application;

use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\CanAccessLicenceVehiclesWithId;

/**
 * Modify
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Modify extends CanAccessLicenceVehiclesWithId
{
    /**
     * @inheritdoc
     */
    public function isValid($dto)
    {
        if ($this->canAccessApplication($dto->getApplication()) === false) {
            return false;
        }

        return parent::isValid($dto);
    }
}
