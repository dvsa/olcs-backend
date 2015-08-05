<?php

/**
 * PiVenue Repository
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Pi\PiVenue as Entity;
use Doctrine\ORM\Query;

/**
 * PiVenue Repository
 */
class PiVenue extends AbstractRepository
{
    protected $entity = Entity::class;
}
