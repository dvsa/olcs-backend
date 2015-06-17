<?php

/**
 * Statement
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Cases\Statement as Entity;

/**
 * Statement
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Statement extends AbstractRepository
{
    protected $entity = Entity::class;
}
