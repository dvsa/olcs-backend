<?php

/**
 * Phone Contact
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\ContactDetails\PhoneContact as Entity;

/**
 * Phone Contact
 */
class PhoneContact extends AbstractRepository
{
    protected $entity = Entity::class;
}
