<?php

/**
 * Application
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Domain\Exception;
use Zend\Stdlib\ArraySerializableInterface as QryCmd;

/**
 * Application
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class Application extends AbstractRepository
{
    protected $entity = '\Dvsa\Olcs\Api\Entity\Application\Application';

    protected function buildDefaultQuery(QueryBuilder $qb, QryCmd $query)
    {
        $this->getQueryBuilder()->modifyQuery($qb)->withRefdata()->byId($query->getId());
    }
}
