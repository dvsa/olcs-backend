<?php

namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Pi\Decision as Entity;

/**
 * Decision
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class Decision extends AbstractRepository
{
    protected $entity = Entity::class;

    protected function applyListFilters(\Doctrine\ORM\QueryBuilder $qb, \Dvsa\Olcs\Transfer\Query\QueryInterface $query)
    {
        if (method_exists($query, 'getIsNi') && !empty($query->getIsNi())) {
            $qb->andWhere($qb->expr()->eq('m.isNi', ':isNi'))
                ->setParameter('isNi', $query->getIsNi() === 'Y');
        }
        if (method_exists($query, 'getGoodsOrPsv') && !empty($query->getGoodsOrPsv())) {
            $qb->andWhere($qb->expr()->eq('m.goodsOrPsv', ':goodsOrPsv'))
                ->setParameter('goodsOrPsv', $query->getGoodsOrPsv());
        }
    }
}
