<?php

namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\System\Category as Entity;

/**
 * Category
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class Category extends AbstractRepository
{
    protected $entity = Entity::class;

    protected function applyListFilters(\Doctrine\ORM\QueryBuilder $qb, \Dvsa\Olcs\Transfer\Query\QueryInterface $query)
    {
        if (method_exists($query, 'getIsTaskCategory') && !empty($query->getIsTaskCategory())) {
            $qb->andWhere($qb->expr()->eq('m.isTaskCategory', ':isTaskCategory'))
                ->setParameter('isTaskCategory', $query->getIsTaskCategory() === 'Y');
        }
        if (method_exists($query, 'getIsDocCategory') && !empty($query->getIsDocCategory())) {
            $qb->andWhere($qb->expr()->eq('m.isDocCategory', ':isDocCategory'))
                ->setParameter('isDocCategory', $query->getIsDocCategory() === 'Y');
        }
        if (method_exists($query, 'getIsScanCategory') && !empty($query->getIsScanCategory())) {
            $qb->andWhere($qb->expr()->eq('m.isScanCategory', ':isScanCategory'))
                ->setParameter('isScanCategory', $query->getIsScanCategory() === 'Y');
        }
    }
}
