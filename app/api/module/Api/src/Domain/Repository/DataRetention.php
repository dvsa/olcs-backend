<?php

namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Transfer\Query\DataRetention\Records;
use Dvsa\Olcs\Api\Entity\DataRetention\DataRetention as DataRetentionEntity;
use Dvsa\Olcs\Transfer\Query\DataRetention\Records as RecordsQry;

/**
 * DataRetention
 */
class DataRetention extends AbstractRepository
{
    protected $entity = DataRetentionEntity::class;

    /**
     * Applies filters to list queries
     *
     * @param QueryBuilder              $qb    doctrine query builder
     * @param QueryInterface|RecordsQry $query the query
     *
     * @return void
     */
    protected function applyListFilters(QueryBuilder $qb, QueryInterface $query)
    {
        if ($query instanceof RecordsQry) {

            if ($query->getMarkedForDeletion() != null) {
                $actionConfirmation = $query->getMarkedForDeletion() == 'Y' ? 1 : 0;
                $qb->andWhere($qb->expr()->eq($this->alias . '.actionConfirmation', ':actionConfirmation'));
                $qb->setParameter('actionConfirmation', $actionConfirmation);
            }

            if ($query->getNextReview() == 'deferred') {
                $qb->andWhere($qb->expr()->isNotNull($this->alias . '.nextReviewDate'));
            } elseif ($query->getNextReview() == 'pending') {
                $qb->andWhere($qb->expr()->isNull($this->alias . '.nextReviewDate'));
            }

            if (is_numeric($query->getAssignedToUser())) {
                $qb->andWhere($qb->expr()->eq($this->alias . '.assignedTo', ':assignedToUser'));
                $qb->setParameter('assignedToUser', $query->getAssignedToUser());
            } elseif ($query->getAssignedToUser() == 'unassigned') {
                $qb->andWhere($qb->expr()->isNull($this->alias . '.assignedTo'));
            }

            $qb->andWhere($qb->expr()->eq('drr.isEnabled', 1));
            $qb->andWhere($qb->expr()->eq($this->alias . '.dataRetentionRule', ':dataRetentionRuleId'));
            $qb->andWhere($qb->expr()->eq('drr.actionType', ':actionType'));
            $qb->setParameter('actionType', 'Review');
            $qb->setParameter('dataRetentionRuleId', $query->getDataRetentionRuleId());

        }

    }

    /**
     * Override to add additional data to the default fetchList() method
     * Join tables to query by conditions
     *
     * @param QueryBuilder $qb Doctrine query builder
     *
     * @return void
     */
    protected function applyListJoins(QueryBuilder $qb)
    {
        $this->getQueryBuilder()
            ->with('dataRetentionRule', 'drr')
            ->with('assignedTo', 'u')
            ->with('u.contactDetails', 'cd')
            ->with('cd.person', 'p');
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

    /**
     * Fetch a list of processed data retention rows for a data retention rule, and date range
     *
     * @param int       $dataRetentionRuleId Data retention rule ID
     * @param \DateTime $startDate           Start date
     * @param \DateTime $endDate             End date
     *
     * @return array
     */
    public function fetchAllProcessedForRule($dataRetentionRuleId, \DateTime $startDate, \DateTime $endDate)
    {
        $qb = $this->createQueryBuilder();

        $qb->andWhere($qb->expr()->eq($this->alias . '.dataRetentionRule', ':dataRetentionRuleId'));
        $qb->andWhere($qb->expr()->gte($this->alias . '.deletedDate', ':startDate'));
        $qb->andWhere($qb->expr()->lte($this->alias . '.deletedDate', ':endDate'));

        $qb->setParameter('dataRetentionRuleId', $dataRetentionRuleId);
        $qb->setParameter('startDate', $startDate);
        $qb->setParameter('endDate', $endDate);

        $this->disableSoftDeleteable([DataRetentionEntity::class]);

        $result = $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);

        $this->enableSoftDeleteable();

        return $result;
    }
}
