<?php

namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Transfer\Query\DataRetention\Records;
use Dvsa\Olcs\Api\Entity\DataRetention\DataRetention as DataRetentionEntity;

/**
 * DataRetention
 */
class DataRetention extends AbstractRepository
{
    protected $entity = DataRetentionEntity::class;

    /**
     * Fetch as list of entities to delete
     *
     * @param int $limit Number of rows to return
     *
     * @return array of DataRetention entities
     */
    public function fetchEntitiesToDelete($limit)
    {
        $qb = $this->createQueryBuilder();

        $this->getQueryBuilder()->modifyQuery($qb);
        $this->getQueryBuilder()->with('dataRetentionRule', 'drr');

        $qb->andWhere($qb->expr()->eq('drr.isEnabled', 1))
            ->andWhere($qb->expr()->eq($this->alias . '.toAction', 1))
            ->andWhere($qb->expr()->eq($this->alias . '.actionConfirmation', 1))
            ->andWhere($qb->expr()->isNull($this->alias . '.actionedDate'))
            ->andWhere($qb->expr()->isNull($this->alias . '.nextReviewDate'));

        $qb->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

    /**
     * Fetch all data retentions with associated rules that are enabled
     *
     * @param QueryInterface|Records $query Query
     *
     * @return array
     */
    public function fetchAllWithEnabledRules(QueryInterface $query = null)
    {
        $qb = $this->createQueryBuilder();

        $this->getQueryBuilder()->modifyQuery($qb);
        $this->getQueryBuilder()->with('dataRetentionRule', 'drr');

        $qb->andWhere($qb->expr()->eq('drr.isEnabled', 1))
            ->andWhere($qb->expr()->eq($this->alias . '.dataRetentionRule', $query->getDataRetentionRuleId()))
            ->andWhere($qb->expr()->isNull($this->alias . '.deletedDate'));

        if (!is_null($query)) {
            $this->buildDefaultListQuery($qb, $query);
        }

        return [
            'results' => $qb->getQuery()->getResult(),
            'count' => $this->getPaginator($qb)->count()
        ];
    }
}
