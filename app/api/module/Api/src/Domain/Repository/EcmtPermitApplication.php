<?php

/**
 * Permit Application
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication as Entity;

/**
 * Permit Application
 */
class EcmtPermitApplication extends AbstractRepository
{
    protected $entity = Entity::class;
}
