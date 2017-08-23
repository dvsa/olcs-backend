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

        $this->getQueryBuilder()
            ->modifyQuery($qb);

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

    /**
     * Run the action proc
     *
     * @param string $storedProc The stored proc to run
     * @param int    $entityId   Entity ID to be actioned
     * @param int    $userId     User ID of the user running the proc
     *
     * @return bool Success
     */
    public function runActionProc($storedProc, $entityId, $userId)
    {
        $callString = sprintf('CALL %s(%d, %d)', $storedProc, $entityId, $userId);
        $results = $this->getEntityManager()->getConnection()->fetchAll($callString);

        $success = true;
        // Search through the output, if contains the word ERROR then assume it was not successful
        array_walk_recursive(
            $results,
            function ($value) use (&$success) {
                $success = $success && (strpos($value, 'ERROR') === false);
            },
            $success
        );

        return $success;
    }
}
