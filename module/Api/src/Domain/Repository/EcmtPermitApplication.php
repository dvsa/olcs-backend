<?php

/**
 * Permit Application
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use \Doctrine\ORM\QueryBuilder;
use \Dvsa\Olcs\Transfer\Query\QueryInterface;

use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication as Entity;

/**
 * Permit Application
 */
class EcmtPermitApplication extends AbstractRepository
{
    protected $entity = Entity::class;

    /**
     * @param QueryBuilder $qb
     * @param QueryInterface $query
     */
    protected function applyListFilters(QueryBuilder $qb, QueryInterface $query)
    {
        $qb->addOrderBy($this->alias . '.' . $query->getSort(), $query->getOrder());
        $qb->andWhere($qb->expr()->in($this->alias . '.status',
            [
                Entity::STATUS_NOT_YET_SUBMITTED,
                Entity::STATUS_UNDER_CONSIDERATION,
                Entity::STATUS_AWAITING_FEE
            ]));
    }
}
