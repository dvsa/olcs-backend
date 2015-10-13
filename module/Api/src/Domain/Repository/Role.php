<?php

/**
 * Role
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\User\Role as Entity;

/**
 * Role
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Role extends AbstractRepository
{
    protected $entity = Entity::class;
}
