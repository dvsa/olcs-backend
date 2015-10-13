<?php

/**
 * Psv Vehicles
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Variation;

use Dvsa\Olcs\Api\Entity;

/**
 * Psv Vehicles
 *
 * @NOTE Mostly the same as Application, however we override the hasBreakdown method to check the model
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class PsvVehicles extends \Dvsa\Olcs\Api\Domain\QueryHandler\Application\PsvVehicles
{
}
