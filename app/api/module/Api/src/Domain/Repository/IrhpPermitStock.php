<?php

/**
 * IrhpPermitStock
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock as Entity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermit as IrhpPermitEntity;

/**
 * IrhpPermitStock
 */
class IrhpPermitStock extends AbstractRepository
{
    protected $entity = Entity::class;

    protected $alias = 'ips';

    /**
     * Retrieves the Irhp Permit Stock
     * that will be valid next
     * (after the stock for the given date has expired)
     *
     * Filtered for a given permit type
     *
     * @param string $permitType
     * @param DateTime $date
     * @param Query::HYDRATE_OBJECT $hydrationMode
     *
     * @return array
     * @throws NotFoundException
     */
    public function getNextIrhpPermitStockByPermitType($permitType, $date, $hydrationMode = Query::HYDRATE_OBJECT)
    {
        $query = $this->getEntityManager()->createQueryBuilder();

        $results = $query->select('ips')
            ->from(Entity::class, 'ips')
            ->innerJoin('ips.irhpPermitType', 'ipt')
            ->where($query->expr()->andX(
                $query->expr()->gte('ips.validFrom', '?1'),
                $query->expr()->eq('ipt.name', '?2')
            ))
            ->setParameter(1, $date)
            ->setParameter(2, $permitType)
            ->orderBy('ips.validTo', 'ASC')
            ->getQuery()
            ->getResult($hydrationMode);

        if (empty($results)) {
            throw new NotFoundException('No stock available.');
        }

        return $results[0];
    }

    /**
     * Returns list of stocks ready to print
     *
     * @return array
     */
    public function fetchReadyToPrint()
    {
        $qb = $this->createQueryBuilder();

        $qb
            ->select($this->alias, 'ipt', 'rd')
            ->innerJoin($this->alias . '.irhpPermitType', 'ipt')
            ->innerJoin('ipt.name', 'rd')
            ->innerJoin($this->alias . '.irhpPermitRanges', 'ipr')
            ->innerJoin('ipr.irhpPermits', 'ip')
            ->Where($qb->expr()->in('ip.status', ':statuses'))
            ->setParameter('statuses', IrhpPermitEntity::$readyToPrintStatuses)
            ->orderBy('rd.displayOrder', 'ASC')
            ->orderBy($this->alias . '.validFrom', 'ASC');

        return $qb->getQuery()->getResult();
    }
}
