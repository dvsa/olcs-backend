<?php
/**
 * EventHistory
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Entity\EventHistory\EventHistory as EventHistoryEntity;
use Dvsa\Olcs\Transfer\Query\Processing\History as HistoryDTO;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * EventHistory
 */
class EventHistory extends AbstractRepository
{
    protected $entity = EventHistoryEntity::class;

    /**
     * @param QueryBuilder $qb
     * @param HistoryDTO $query
     */
    protected function applyListFilters(QueryBuilder $qb, QueryInterface $query)
    {
        if ($query->getCase() !== null) {
            $qb->andWhere($this->alias . '.case = :caseId');
            $qb->setParameter('caseId', $query->getCase());
        }

        $this->getQueryBuilder()->modifyQuery($qb)->with('eventHistoryType')->withUser();
    }
}
