<?php

/**
 * Publication
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Entity\Publication\Publication as Entity;

/**
 * Publication
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Publication extends AbstractRepository
{
    protected $entity = Entity::class;
}
