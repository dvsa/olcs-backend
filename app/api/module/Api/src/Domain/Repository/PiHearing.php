<?php

/**
 * PiHearing
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Pi\PiHearing as Entity;

/**
 * PiHearing
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class PiHearing extends AbstractRepository
{
    protected $entity = Entity::class;
}
