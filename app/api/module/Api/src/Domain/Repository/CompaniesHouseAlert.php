<?php

namespace Dvsa\Olcs\Api\Domain\Repository;

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
    protected $entity = \Dvsa\Olcs\Api\Entity\CompaniesHouse\CompaniesHouseAlert::class;

    protected $alias = 'ca';

    public function fetchCaListWithLicences(PagedQueryInterface $query)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $sqlQuery = $qb->select('cha', 'cha_o', 'cha_o_ls')
            ->from($this->entity, 'cha')
            ->innerJoin('cha.organisation', 'cha_o')
            ->innerJoin('cha_o.licences', 'cha_o_ls')
            ->where(
                $qb->expr()->in(
                    'cha_o_ls.status',
                    [
                        Entity\Licence\Licence::LICENCE_STATUS_CURTAILED,
                        Entity\Licence\Licence::LICENCE_STATUS_VALID,
                        Entity\Licence\Licence::LICENCE_STATUS_SUSPENDED
                    ]

                )
            )
            ->setFirstResult($query->getLimit() * ($query->getPage()-1))
            ->setMaxResults($query->getLimit())
            ->getQuery();
        return $sqlQuery->execute([], Query::HYDRATE_OBJECT);
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
