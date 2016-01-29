<?php

/**
 * CompaniesHouseAlert
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Domain\Exception;
use Dvsa\Olcs\Api\Entity\CompaniesHouse\CompaniesHouseAlert as Entity;
use Dvsa\Olcs\Api\Entity\System\RefData as RefDataEntity;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * CompaniesHouseAlert
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class CompaniesHouseAlert extends AbstractRepository
{
    protected $entity = Entity::class;

    protected $alias = 'ca';

    protected function applyListFilters(QueryBuilder $qb, QueryInterface $query)
    {
        if (!$query->getIncludeClosed()) {
            $qb->andWhere($qb->expr()->eq($this->alias . '.isClosed', 0));
        }

        if ($query->getTypeOfChange()) {
            $qb
                ->innerJoin(
                    $this->alias.'.reasons',
                    'r',
                    Join::WITH,
                    $qb->expr()->eq('r.reasonType', ':reasonType')
                )
                ->setParameter('reasonType', $query->getTypeOfChange());
        }
    }

    public function getReasonValueOptions()
    {
        $qb = $this->getEntityManager()->getRepository(RefDataEntity::class)->createQueryBuilder('r');
        $qb
            ->where($qb->expr()->eq('r.refDataCategoryId', ':categoryId'))
            ->setParameter('categoryId', 'ch_alert_reason');

        $results = $qb->getQuery()->getArrayResult();

        $valueOptions = [];
        foreach ($results as $result) {
            $valueOptions[$result['id']] = $result['description'];
        }
        return $valueOptions;
    }
}
