<?php

/**
 * Organisation
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Organisation\Organisation as Entity;
use Doctrine\ORM\QueryBuilder;
use Zend\Stdlib\ArraySerializableInterface as QryCmd;

/**
 * Organisation
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Organisation extends AbstractRepository
{
    protected $entity = Entity::class;

    /**
     * @NOTE This method can be overridden to extend the default resource bundle
     *
     * @param QueryBuilder $qb
     * @param QryCmd $query
     */
    protected function buildDefaultQuery(QueryBuilder $qb, QryCmd $query)
    {
        $queryBuilder = parent::buildDefaultQuery($qb, $query);

        //$qb->addSelect('')
    }
}
