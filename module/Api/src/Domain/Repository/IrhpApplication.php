<?php

/**
 * IrhpApplication
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as Entity;

class IrhpApplication extends AbstractRepository
{
    protected $entity = Entity::class;
}
