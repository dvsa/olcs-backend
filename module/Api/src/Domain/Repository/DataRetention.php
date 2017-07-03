<?php

namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity\DataRetention as Entity;

/**
 * DataRetention
 */
class DataRetention extends AbstractRepository
{
    protected $entity = Entity::class;

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
}
