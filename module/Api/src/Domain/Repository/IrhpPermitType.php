<?php

/**
 * IRHP Permit Type
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType as Entity;
use DateTime;

/**
 * Irhp Permit Type
 */
class IrhpPermitType extends AbstractRepository
{
    protected $entity = Entity::class;
    protected $alias = 'ipt';

    /**
     * Returns list of types with currently open windows
     *
     * @param DateTime $now Now
     *
     * @return array
     */
    public function fetchAvailableTypes(DateTime $now)
    {
        $qb = $this->createQueryBuilder();

        $qb
            ->select($this->alias, 'rd')
            ->innerJoin($this->alias.'.name', 'rd')
            ->innerJoin($this->alias.'.irhpPermitStocks', 'ips')
            ->innerJoin('ips.irhpPermitWindows', 'ipw')
            ->where($qb->expr()->lte('ipw.startDate', ':now'))
            ->andWhere($qb->expr()->gt('ipw.endDate', ':now'))
            ->setParameter('now', $now->format(DateTime::ISO8601))
            ->orderBy('rd.displayOrder', 'ASC');

        return $qb->getQuery()->getResult();
    }
}
