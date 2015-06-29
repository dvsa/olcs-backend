<?php

/**
 * Organisation Person
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Organisation\OrganisationPerson as Entity;

/**
 * Organisation Person
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class OrganisationPerson extends AbstractRepository
{
    protected $entity = Entity::class;
}
