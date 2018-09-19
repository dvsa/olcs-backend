<?php

namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock as Entity;
use Doctrine\ORM\Query;


/**
 * Feature toggle
 */
class IrhpPermitStock extends AbstractRepository
{
    protected $entity = Entity::class;

    /**
     * Retrieves the Irhp Permit Stock
     * that will be valid next
     * (after the stock for the given date has expired)
     *
     * Filtered for a given permit type
     */
    public function getNextIrhpPermitStockByPermitType($permitType, $date, $hydrateMode = Query::HYDRATE_OBJECT)
    {
        return $this->getEntityManager()->createQueryBuilder()
                ->select('ips')
                ->from(Entity::class, 'ips')
                ->innerJoin('ips.irhpPermitType', 'ipt')
                ->where('ips.validTo >= ?1') //stock is valid
                ->where('ips.validFrom >= ?1') //stock starts in future
                ->andWhere('ipt.name = ?2') //Permit Type ECMT
                ->orderBy('ips.validTo', 'ASC')
                ->setParameter(1, $date)
                ->setParameter(2, $permitType)
                ->setMaxResults(1) //There should only ever be one, take the most recent
                ->getQuery()
                ->getResult($hydrateMode);
    }
}
