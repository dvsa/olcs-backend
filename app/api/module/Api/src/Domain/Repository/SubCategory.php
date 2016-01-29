<?php

namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\System\SubCategory as Entity;

/**
 * SubCategory
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class SubCategory extends AbstractRepository
{
    protected $entity = Entity::class;

    protected function applyListFilters(\Doctrine\ORM\QueryBuilder $qb, \Dvsa\Olcs\Transfer\Query\QueryInterface $query)
    {
        if (method_exists($query, 'getIsTaskCategory') && !empty($query->getIsTaskCategory())) {
            $qb->andWhere($qb->expr()->eq('m.isTask', ':isTaskCategory'))
                ->setParameter('isTaskCategory', $query->getIsTaskCategory() === 'Y');
        }
        if (method_exists($query, 'getIsDocCategory') && !empty($query->getIsDocCategory())) {
            $qb->andWhere($qb->expr()->eq('m.isDoc', ':isDocCategory'))
                ->setParameter('isDocCategory', $query->getIsDocCategory() === 'Y');
        }
        if (method_exists($query, 'getIsScanCategory') && !empty($query->getIsScanCategory())) {
            $qb->andWhere($qb->expr()->eq('m.isScan', ':isScanCategory'))
                ->setParameter('isScanCategory', $query->getIsScanCategory() === 'Y');
        }
        if (method_exists($query, 'getCategory') && !empty($query->getCategory())) {
            $qb->andWhere($qb->expr()->eq('m.category', ':category'))
                ->setParameter('category', $query->getCategory());
        }
    }
}
