<?php

/**
 * Vehicle
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Vehicle\Vehicle as Entity;

/**
 * Vehicle
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Vehicle extends AbstractRepository
{
    protected $entity = Entity::class;
}
