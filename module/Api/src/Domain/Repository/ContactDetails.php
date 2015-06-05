<?php

/**
 * ContactDetails
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails as Entity;

/**
 * ContactDetails
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ContactDetails extends AbstractRepository
{
    protected $entity = Entity::class;
}
