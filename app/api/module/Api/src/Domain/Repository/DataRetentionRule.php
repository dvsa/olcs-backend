<?php

namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\DataRetentionRule as Entity;

/**
 * DataRetentionRule
 */
class DataRetentionRule extends AbstractRepository
{
    protected $entity = Entity::class;

    /**
     * Fetch as list of enabled data retention rules
     *
     * @return array
     */
    public function fetchEnabledRules()
    {
        $qb = $this->createQueryBuilder();

        $this->getQueryBuilder()
            ->modifyQuery($qb);

        $qb->andWhere($qb->expr()->eq($this->alias .'.isEnabled', 1));

        return $qb->getQuery()->getResult();
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
