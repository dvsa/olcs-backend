<?php

namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock as Entity;

/**
 * Feature toggle
 */
class IrhpPermitStock extends AbstractRepository
{
    protected $entity = Entity::class;
}
