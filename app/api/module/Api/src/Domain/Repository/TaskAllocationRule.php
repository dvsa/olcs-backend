<?php

/**
 * Task Allocation Rule
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */

namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Entity\Task\TaskAllocationRule as Entity;

/**
 * Task Allocation Rule
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class TaskAllocationRule extends AbstractRepository
{
    protected $entity = Entity::class;

    /**
     * Fetch allocation rules by parameters
     *
     * @param int $categoryId
     * @param string $operatorType
     * @param string $ta
     * @param bool $isMlh
     * @return array
     */
    public function fetchByParameters($categoryId, $operatorType = null, $ta = null, $isMlh = null)
    {
        $qb = $this->createQueryBuilder();

        $qb->andWhere($qb->expr()->eq('m.category', ':category'))
            ->setParameter('category', $categoryId);

        if ($operatorType) {
            $qb->andWhere($qb->expr()->eq('m.goodsOrPsv', ':operatorType'))
                ->setParameter('operatorType', $operatorType);
        } else {
            $qb->andWhere($qb->expr()->isNull('m.goodsOrPsv'));
        }

        if ($ta) {
            $qb->andWhere($qb->expr()->eq('m.trafficArea', ':trafficArea'))
                ->setParameter('trafficArea', $ta);
        } else {
            $qb->andWhere($qb->expr()->isNull('m.trafficArea'));
        }

        if ($isMlh !== null) {
            $qb->andWhere($qb->expr()->eq('m.isMlh', ':isMlh'))
                ->setParameter('isMlh', $isMlh);
        } else {
            $qb->andWhere($qb->expr()->isNull('m.isMlh'));
        }
        return $qb->getQuery()->getResult(Query::HYDRATE_OBJECT);
    }

    /**
     * Build defailt list query
     *
     * @param \Doctrine\ORM\QueryBuilder $qb
     * @param \Dvsa\Olcs\Transfer\Query\QueryInterface $query
     * @param array $compositeFields
     */
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
