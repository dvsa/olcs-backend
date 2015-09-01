<?php

namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Organisation\OrganisationUser as Entity;

/**
 * Organisation User
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class OrganisationUser extends AbstractRepository
{
    protected $entity = Entity::class;
}
