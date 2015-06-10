<?php

/**
 * Task Allocation Rule
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Api\Entity\Task\TaskAllocationRule as Entity;

/**
 * Task Allocation Rule
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TaskAllocationRule extends AbstractRepository
{
    protected $entity = Entity::class;

    public function fetchForSimpleTaskAssignment(Category $category)
    {
        $qb = $this->createQueryBuilder();

        $this->getQueryBuilder()->modifyQuery($qb)->withRefdata();

        $qb->andWhere(
            $qb->expr()->eq('m.category', ':category')
        )->andWhere(
            $qb->expr()->isNull('m.isMlh')
        )->andWhere(
            $qb->expr()->isNull('m.trafficArea')
        )->setParameter('category', $category->getId());

        return $qb->getQuery()->getResult(Query::HYDRATE_OBJECT);
    }
}
