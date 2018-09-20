<?php

/**
 * IRHP Permit Type
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType as Entity;

/**
 * Irhp Permit Type
 */
class IrhpPermitType extends AbstractRepository
{
    protected $entity = Entity::class;
}
