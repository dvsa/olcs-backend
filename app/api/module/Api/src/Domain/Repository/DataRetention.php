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

        $qb->andWhere($qb->expr()->eq('drr.isEnabled', 1));
        $qb->andWhere($qb->expr()->eq($this->alias . '.dataRetentionRule', $query->getDataRetentionRuleId()));
        $qb->andWhere($qb->expr()->isNull($this->alias . '.deletedDate'));

        $qb->andWhere($qb->expr()->eq('drr.actionType', ':actionType'));
        $qb->setParameter('actionType', 'Review');

        if (!is_null($query)) {
            $this->buildDefaultListQuery($qb, $query);
        }

        return [
            'results' => $qb->getQuery()->getResult(),
            'count' => $this->getPaginator($qb)->count()
        ];
    }

    /**
     * Run the Data Retention cleanup stored proc,
     * NB Warning this can delete a lot of data
     *
     * @param int  $limit  Number of data retention rows to process
     * @param int  $userId User who will be audited as running the data retention deletes
     * @param bool $dryRun If true then no rows are actually deleted
     *
     * @return bool
     */
    public function runCleanupProc($limit, $userId, $dryRun = false)
    {
        $connection = $this->getEntityManager()->getConnection();
        $statement = $connection->prepare(
            sprintf('CALL sp_dr_cleanup(%d, %d, %d)', $userId, $limit, $dryRun)
        );

        return $statement->execute();
    }
}
