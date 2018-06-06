<?php

/**
 * Application status
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\ApplicationStatus as Entity;

/**
 * Application status
 */
class ApplicationStatus extends AbstractRepository
{
    protected $entity = Entity::class;
}
