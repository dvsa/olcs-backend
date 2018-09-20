<?php

/**
 * ECMT Permits
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Permits\EcmtPermits as Entity;

/**
 * ECMT Permits
 */
class EcmtPermits extends AbstractRepository
{
    protected $entity = Entity::class;
}
