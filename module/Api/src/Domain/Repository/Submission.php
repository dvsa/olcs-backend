<?php

namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Submission\Submission as Entity;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Submission
 *
 * @author Shaun Lizzio <shaun.lizzio@valtech.co.uk>
 */
class Submission extends AbstractRepository
{
    protected $entity = Entity::class;

    /**
     * Overridden default query to return appropriate table joins
     * @param QueryBuilder $qb
     * @param int $id
     * @return \Dvsa\Olcs\Api\Domain\QueryBuilder
     */
    protected function buildDefaultQuery(QueryBuilder $qb, $id)
    {
        return $this->getQueryBuilder()->modifyQuery($qb)
            ->withRefdata()
            ->with('recipientUser', 'r')
            ->with('r.contactDetails', 'rcd')
            ->with('rcd.person')
            ->with('senderUser', 's')
            ->with('s.contactDetails', 'scd')
            ->with('scd.person')
            ->with('documents', 'd')
            ->with('submissionSectionComments', 'ssc')
            ->with('ssc.submissionSection', 'sc')
            ->with('submissionActions', 'sa')
            ->with('sa.actionTypes')
            ->with('sa.reasons')
            ->byId($id);
    }

    /**
     * Override to add additional data to the default fetchList() method
     * @param QueryBuilder $qb
     */
    protected function applyListJoins(QueryBuilder $qb)
    {
        $this->getQueryBuilder()
            ->with('recipientUser', 'r')
            ->with('r.contactDetails', 'rcd')
            ->with('rcd.person')
            ->with('senderUser', 's')
            ->with('s.contactDetails', 'scd')
            ->with('scd.person');
    }

    /**
     * Apply List Filters
     * @param QueryBuilder $qb
     * @param QueryInterface $query
     */
    protected function applyListFilters(QueryBuilder $qb, QueryInterface $query)
    {
        $qb->andWhere($qb->expr()->eq($this->alias . '.case', ':byCase'))
            ->setParameter('byCase', $query->getCase());
    }
}
