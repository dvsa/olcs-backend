<?php

/**
 * EventHistory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Entity\EventHistory\EventHistory as EventHistoryEntity;
use Dvsa\Olcs\Transfer\Query\QueryInterface;


/**
 * EventHistory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class EventHistory extends AbstractRepository
{
    protected $entity = EventHistoryEntity::class;

    protected function applyListFilters(QueryBuilder $qb, QueryInterface $query)
    {
        if ($query->getCaseId() !== null) {
            $qb->andWhere($this->alias . '.case = :caseId');
            $qb->setParameter('caseId', $query->getCaseId());
        }

        $this->getQueryBuilder()->modifyQuery($qb)->with('eventHistoryType')->withUser();
    }
}
