<?php

/**
 * IrhpPermitApplication
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Exception;
use Zend\Stdlib\ArraySerializableInterface as QryCmd;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication as Entity;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * IrhpPermitApplication
 *
 * @TODO: Replace this query with the bundle & listFilter design pattern.
 */
class IrhpPermitApplication extends AbstractRepository
{
    protected $entity = Entity::class;
}
