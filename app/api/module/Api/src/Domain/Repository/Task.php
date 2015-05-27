<?php

/**
 * Task
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Task\Task as Entity;

/**
 * Task
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Task extends AbstractRepository
{
    protected $entity = Entity::class;
}
