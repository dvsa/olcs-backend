<?php

namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Permits\IrhpPermit as Entity;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Transfer\Query\IrhpPermit\GetList;
use Dvsa\Olcs\Transfer\Query\IrhpPermit\GetListByEcmtId;
use Dvsa\Olcs\Transfer\Query\Permits\ReadyToPrint;
use Dvsa\Olcs\Transfer\Query\Permits\ValidEcmtPermits;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * IRHP Permit
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class IrhpPermit extends AbstractRepository
{
    protected $entity = Entity::class;

    /**
     * Returns the count of permits in the specified stock
     *
     * @param int $stockId
     *
     * @return int
     */
    public function getPermitCount($stockId)
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('count(ip.id)')
            ->from(Entity::class, 'ip')
            ->innerJoin('ip.irhpPermitRange', 'ipr')
            ->where('IDENTITY(ipr.irhpPermitStock) = ?1')
            ->setParameter(1, $stockId)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Returns the count of permits in the specified range
     *
     * @param int $rangeId
     *
     * @return int
     */
    public function getPermitCountByRange($rangeId)
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('count(ip.id)')
            ->from(Entity::class, 'ip')
            ->where('IDENTITY(ip.irhpPermitRange) = ?1')
            ->setParameter(1, $rangeId)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Apply List Filters
     *
     * @param QueryBuilder   $qb    Doctrine Query Builder
     * @param QueryInterface $query Http Query
     *
     * @return void
     */
    protected function applyListFilters(QueryBuilder $qb, QueryInterface $query)
    {
        if ($query instanceof ValidEcmtPermits) {
            $qb->andWhere($qb->expr()->eq('ipa.ecmtPermitApplication', ':ecmtId'))
                ->setParameter('ecmtId', $query->getId());
            $qb->orderBy($this->alias . '.permitNumber', 'DESC');
        } elseif ($query instanceof ReadyToPrint) {
            $qb->andWhere($qb->expr()->in($this->alias . '.status', ':statuses'))
                ->setParameter(
                    'statuses',
                    [
                        Entity::STATUS_PENDING,
                        Entity::STATUS_AWAITING_PRINTING,
                        Entity::STATUS_PRINTING,
                        Entity::STATUS_ERROR,
                    ]
                );
            $qb->orderBy($this->alias . '.permitNumber', 'ASC');
        }

        if (($query instanceof GetList) && ($query->getIrhpPermitApplication() != null)) {
            $qb->andWhere($qb->expr()->eq($this->alias . '.irhpPermitApplication', ':irhpPermitApplication'));
            $qb->setParameter('irhpPermitApplication', $query->getIrhpPermitApplication());
        }

        if (($query instanceof GetListByEcmtId) && ($query->getEcmtPermitApplication() != null)) {
            $qb->andWhere($qb->expr()->eq('ipa.ecmtPermitApplication', ':ecmtId'))
                ->setParameter('ecmtId', $query->getEcmtPermitApplication());
        }
    }

    /**
     * Add List Joins
     *
     * @param QueryBuilder $qb Doctrine Query Builder
     *
     * @return void
     */
    protected function applyListJoins(QueryBuilder $qb)
    {
        $this->getQueryBuilder()->modifyQuery($qb)
            ->with('irhpPermitApplication', 'ipa');
    }

    /**
     * @param int $permitNumber
     * @param int $permitRange
     * @return mixed
     */
    public function fetchByNumberAndRange($permitNumber, $permitRange)
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('ip')
            ->from(Entity::class, 'ip')
            ->where('ip.permitNumber = ?1')
            ->andWhere('ip.irhpPermitRange = ?2')
            ->setParameter(1, $permitNumber)
            ->setParameter(2, $permitRange)
            ->getQuery()->execute();
    }
}
