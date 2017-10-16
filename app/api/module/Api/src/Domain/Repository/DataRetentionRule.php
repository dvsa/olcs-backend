<?php

namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\DataRetentionRule as Entity;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * DataRetentionRule
 */
class DataRetentionRule extends AbstractRepository
{
    protected $entity = Entity::class;

    /**
     * Fetch as list of enabled data retention rules
     *
     * @param QueryInterface $query    Query from API
     * @param bool           $isReview Only retrieve rules in Review status
     *
     * @return array
     */
    public function fetchEnabledRules(QueryInterface $query = null, $isReview = false)
    {
        $qb = $this->createQueryBuilder();

        $this->getQueryBuilder()->modifyQuery($qb);

        $qb->andWhere($qb->expr()->eq($this->alias .'.isEnabled', 1));
        $qb->andWhere($qb->expr()->isNull($this->alias .'.deletedDate'));

        if ($isReview) {
            $qb->andWhere($qb->expr()->eq($this->alias .'.actionType', ':actionType'));
            $qb->setParameter('actionType', 'Review');
        }

        if (!is_null($query)) {
            $this->buildDefaultListQuery($qb, $query);
        }

        return [
            'results' => $qb->getQuery()->getResult(),
            'count' => $this->getPaginator($qb)->count()
        ];
    }

    /**
     * Fetch as list of all data retention rules that have not been deleted
     *
     * @param QueryInterface $query    Query from API
     *
     * @return array
     */
    public function fetchAllNotDeletedRules(QueryInterface $query = null)
    {
        $qb = $this->createQueryBuilder();

        $this->getQueryBuilder()->modifyQuery($qb);

        $qb->andWhere($qb->expr()->isNull($this->alias .'.deletedDate'));

        if (!is_null($query)) {
            $this->buildDefaultListQuery($qb, $query);
        }

        return [
            'results' => $qb->getQuery()->getResult(),
            'count' => $this->getPaginator($qb)->count()
        ];
    }

    /**
     * Run a stored proc
     *
     * @param string $storedProc The stored proc to run
     * @param int    $userId     User ID os the user running the proc
     *
     * @return int number of rows affected
     */
    public function runProc($storedProc, $userId)
    {
        $callString = sprintf('CALL %s(%d)', $storedProc, (int)$userId);
        return $this->getEntityManager()->getConnection()->exec($callString);
    }
}
