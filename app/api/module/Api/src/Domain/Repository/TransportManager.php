<?php

/**
 * TransportManager
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Tm\TransportManager as Entity;

/**
 * TransportManager
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TransportManager extends AbstractRepository
{
    protected $entity = Entity::class;
}
