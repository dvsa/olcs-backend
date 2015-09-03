<?php

/**
 * PiHearing
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Pi\PiHearing as Entity;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * PiHearing
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class PiHearing extends AbstractRepository
{
    protected $entity = Entity::class;

    public function fetchPreviousHearing($pi, $hearingDate)
    {
        $qb = $this->createQueryBuilder();

        $qb->andWhere($qb->expr()->lt($this->alias . '.hearingDate', ':hearingDate'))
        ->andWhere($qb->expr()->eq($this->alias . '.pi', ':pi'))
        ->andWhere($qb->expr()->eq($this->alias . '.isAdjourned', ':isAdjourned'))
        ->setParameter('hearingDate', $hearingDate)
        ->setParameter('pi', $pi)
        ->setParameter('isAdjourned', 1)
        ->orderBy($this->alias . '.hearingDate', 'DESC')
        ->setMaxResults(1);

        $this->getQueryBuilder()->modifyQuery($qb);

        $result = $qb->getQuery()->getResult(Query::HYDRATE_OBJECT);

        if (empty($result)) {
            return $result;
        }

        return $result[0];
    }

    /**
     * Applies a case filter
     * @param QueryBuilder $qb
     * @param QueryInterface $query
     */
    protected function applyListFilters(QueryBuilder $qb, QueryInterface $query)
    {
        $qb->andWhere($qb->expr()->eq($this->alias . '.pi', ':byPi'))
            ->setParameter('byPi', $query->getPi());
    }
}
