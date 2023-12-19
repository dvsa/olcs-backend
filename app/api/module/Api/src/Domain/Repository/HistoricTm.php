<?php

/**
 * Historic TM
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */

namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Tm\HistoricTm as Entity;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Historic TM Repo
 */
class HistoricTm extends AbstractRepository
{
    protected $entity = Entity::class;
    protected $alias = 'htm';

    /**
     * @param QueryBuilder $qb
     * @param QueryInterface $query
     */
    protected function applyListFilters(QueryBuilder $qb, QueryInterface $query)
    {
        $qb->andWhere($qb->expr()->eq($this->alias . '.historicId', ':historicId'))
            ->setParameter('historicId', $query->getHistoricId());
    }
}
