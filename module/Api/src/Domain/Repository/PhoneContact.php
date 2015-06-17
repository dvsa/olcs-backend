<?php

/**
 * Phone Contact
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\ContactDetails\PhoneContact as Entity;

/**
 * Phone Contact
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class PhoneContact extends AbstractRepository
{
    protected $entity = Entity::class;
}
