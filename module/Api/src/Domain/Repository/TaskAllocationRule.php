<?php

/**
 * Task Allocation Rule
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Entity\System\Category as CategoryEntity;
use Dvsa\Olcs\Api\Entity\Task\TaskAllocationRule as Entity;

/**
 * Task Allocation Rule
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TaskAllocationRule extends AbstractRepository
{
    protected $entity = Entity::class;

    public function fetchForSimpleTaskAssignment(CategoryEntity $category)
    {
        $qb = $this->createQueryBuilder();

        $qb->andWhere(
            $qb->expr()->eq('m.category', ':category')
        )->andWhere(
            $qb->expr()->isNull('m.isMlh')
        )->andWhere(
            $qb->expr()->isNull('m.trafficArea')
        )->setParameter('category', $category->getId());

        return $qb->getQuery()->getResult(Query::HYDRATE_OBJECT);
    }

    public function buildDefaultListQuery(
        \Doctrine\ORM\QueryBuilder $qb,
        \Dvsa\Olcs\Transfer\Query\QueryInterface $query,
        $compositeFields = array()
    ) {
        // add calculated columns to allow ordering by them
        parent::buildDefaultListQuery($qb, $query, ['categoryDescription', 'criteria', 'trafficAreaName']);

        $queryBuilderHelper = $this->getQueryBuilder();
        $queryBuilderHelper->with('category', 'cat');
        $queryBuilderHelper->with('goodsOrPsv', 'gop');
        $queryBuilderHelper->with('trafficArea', 'ta');
        $qb->addSelect('cat.description as HIDDEN categoryDescription');
        $qb->addSelect('gop.id as HIDDEN criteria');
        $qb->addSelect('ta.name as HIDDEN trafficAreaName');
    }
}
