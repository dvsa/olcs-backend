<?php

/**
 * LicenceVehicle.php
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Exception;
use Dvsa\Olcs\Api\Entity\Licence\LicenceVehicle as Entity;

/**
 * Licence Vehicle
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceVehicle extends AbstractRepository
{
    protected $entity = Entity::class;
}
