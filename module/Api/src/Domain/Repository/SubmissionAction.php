<?php

/**
 * SubmissionAction
 */

namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Entity\Submission\SubmissionAction as Entity;

/**
 * SubmissionAction
 */
class SubmissionAction extends AbstractRepository
{
    protected $entity = Entity::class;

    /**
     * @param QueryBuilder $qb
     * @param int          $id
     */
    protected function buildDefaultQuery(QueryBuilder $qb, $id)
    {
        parent::buildDefaultQuery($qb, $id);

        $this->getQueryBuilder()->with('reasons');
    }
}
