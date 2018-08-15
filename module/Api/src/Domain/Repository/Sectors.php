<?php

/**
 * Sectors
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Permits\Sectors as Entity;
use \Doctrine\ORM\QueryBuilder;
use \Dvsa\Olcs\Transfer\Query\QueryInterface;


/**
 * Sectors
 */
class Sectors extends AbstractRepository
{
    protected $entity = Entity::class;

    /**
     * @param QueryBuilder $qb
     * @param QueryInterface $query
     */
    protected function applyListFilters(QueryBuilder $qb, QueryInterface $query)
    {
        $qb->addOrderBy($this->alias . '.' . $query->getSort(), $query->getOrder());
    }

}
