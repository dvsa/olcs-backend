<?php

namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Submission\Submission as Entity;
use Doctrine\ORM\QueryBuilder;

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
            ->with('rcd.person');
    }
}
