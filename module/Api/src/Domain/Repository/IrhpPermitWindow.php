<?php

namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitWindow as Entity;
use Doctrine\ORM\Query;

/**
 * Feature toggle
 */
class IrhpPermitWindow extends AbstractRepository
{
    protected $entity = Entity::class;
}
