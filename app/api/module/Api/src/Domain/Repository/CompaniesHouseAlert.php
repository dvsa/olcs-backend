<?php

namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Entity;
use Dvsa\Olcs\Transfer\Query\PagedQueryInterface;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * CompaniesHouseAlert
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class CompaniesHouseAlert extends AbstractRepository
{
    protected $entity = Entity\CompaniesHouse\CompaniesHouseAlert::class;

    protected $alias = 'ca';

    public function fetchCaListWithLicences(PagedQueryInterface $query)
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('cha', 'cha_o')
            ->from(Entity\CompaniesHouse\CompaniesHouseAlert::class, 'cha')
            ->innerJoin('cha.organisation', 'cha_o')
            ->innerJoin('cha_o.licences', 'cha_o_ls')
            ->setFirstResult($query->getLimit() * $query->getPage())
            ->setMaxResults($query->getLimit())
            ->getQuery()->execute([], Query::HYDRATE_OBJECT);
    }


    /**
     * Apply List Filters
     *
     * @param QueryBuilder                                       $qb    Doctrine Query
     * @param \Dvsa\Olcs\Transfer\Query\CompaniesHouse\AlertList $query Http Query
     *
     * @return void
     */
    protected function applyListFilters(QueryBuilder $qb, QueryInterface $query)
    {
        $qb->innerJoin($this->alias . '.organisation', 'o', Join::WITH);
        $qb->innerJoin('o.licences', 'l');

        if (!$query->getIncludeClosed()) {
            $qb->andWhere($qb->expr()->eq($this->alias . '.isClosed', 0));
        }

        if ($query->getTypeOfChange()) {
            $qb
                ->innerJoin(
                    $this->alias . '.reasons',
                    'r',
                    Join::WITH,
                    $qb->expr()->eq('r.reasonType', ':reasonType')
                )
                ->setParameter('reasonType', $query->getTypeOfChange());
        }
    }

    /**
     * Get Reason Value Options
     *
     * @return array
     */
    public function getReasonValueOptions()
    {
        /** @var \Doctrine\ORM\EntityRepository $repo */
        $repo = $this->getEntityManager()->getRepository(Entity\System\RefData::class);

        $qb = $repo->createQueryBuilder('r');
        $qb
            ->where($qb->expr()->eq('r.refDataCategoryId', ':CATEGORY_ID'))
            ->setParameter('CATEGORY_ID', 'ch_alert_reason');

        $results = $qb->getQuery()->getArrayResult();

        $valueOptions = [];
        foreach ($results as $result) {
            $valueOptions[$result['id']] = $result['description'];
        }

        return $valueOptions;
    }
}
