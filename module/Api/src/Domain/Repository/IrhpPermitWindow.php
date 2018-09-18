<?php

namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitWindow as Entity;
use Doctrine\ORM\Query;

/**
 * Feature toggle
 */
class IrhpPermitWindow extends AbstractRepository
{
    protected $entity = Entity::class;

    /**
     * Retrieves the currently open Irhp Permit Window
     * as well as Irhp Permit Stock information.
     *
     * Filtered for windows linked to ECMT permits.
     */
    public function getCurrentIrhpPermitWindow($permitType, $hydrateMode = Query::HYDRATE_OBJECT)
    {
        return $this->getEntityManager()->createQueryBuilder()
                ->select('ipw, ips')
                ->from(Entity::class, 'ipw')
                ->innerJoin('ipw.irhpPermitStock', 'ips')
                ->innerJoin('ips.irhpPermitType', 'ipt')
                ->where('ipw.endDate > ?1') //window is currently open
                ->andWhere('ipt.name = ?2') //Permit Type ECMT
                ->setParameter(1, date("Y-m-d"))
                ->setParameter(2, $permitType)
                ->getQuery()
                ->getResult($hydrateMode);
    }
}
