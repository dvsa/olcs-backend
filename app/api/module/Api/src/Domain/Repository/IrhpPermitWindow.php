<?php

namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitWindow as Entity;
use DateTime;

/**
 * IRHP Permit Window
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class IrhpPermitWindow extends AbstractRepository
{
    protected $entity = Entity::class;

    /**
     * Returns an array of IrhpPermitWindow objects that are open as of the specified date and time
     *
     * @param DateTime $currentDateTime
     *
     * @return array
     */
    public function fetchOpenWindows(DateTime $currentDateTime)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        return $qb->select('ipw')
            ->from(Entity::class, 'ipw')
            ->add(
                'where',
                $qb->expr()->between('?1', 'ipw.startDate', 'ipw.endDate')
            )
            ->setParameter(1, $currentDateTime)
            ->getQuery()
            ->getResult(Query::HYDRATE_ARRAY);
    }

    /**
     * Returns the IrhpPermitWindow that was most recently open prior to the specified date and time, or null if there
     * were no windows open prior to the specified date
     *
     * @param DateTime $currentDateTime
     *
     * @return Entity|null
     */
    public function fetchLastOpenWindow(DateTime $currentDateTime)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        return $qb->select('ipw')
            ->from(Entity::class, 'ipw')
            ->add(
                'where',
                $qb->expr()->lt('ipw.endDate', '?1')
            )
            ->orderBy('ipw.endDate', 'DESC')
            ->setParameter(1, $currentDateTime)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Fetch Windows by Permit Stock ID
     *
     * @param $irhpPermitStockId
     * @return array
     */
    public function fetchByIrhpPermitStockId($irhpPermitStockId)
    {
        $doctrineQb = $this->createQueryBuilder();
        $doctrineQb
            ->andWhere($doctrineQb->expr()->eq($this->alias . '.irhpPermitStock', ':irhpPermitStock'))
            ->setParameter('irhpPermitStock', $irhpPermitStockId);
        return $doctrineQb->getQuery()->getResult();
    }


    /**
     * Fetch Overlapping Windows by Permit Stock ID, proposed startDate and proposed End Date
     *
     * @param $irhpPermitStock
     * @param $proposedStartDate
     * @param $proposedEndDate
     * @param null $irhpPermitWindow
     * @return array
     */
    public function findOverlappingWindowsByType($irhpPermitStock, $proposedStartDate, $proposedEndDate, $irhpPermitWindow = null)
    {
        $doctrineQb = $this->createQueryBuilder();
        $doctrineQb
            ->orWhere($doctrineQb->expr()->between($this->alias . '.startDate', ':proposedStartDate', ':proposedEndDate'))
            ->orWhere($doctrineQb->expr()->between($this->alias . '.endDate', ':proposedStartDate', ':proposedEndDate'))
            ->orWhere($doctrineQb->expr()->between(':proposedStartDate', $this->alias . '.startDate', $this->alias . '.endDate'))
            ->andWhere($doctrineQb->expr()->eq($this->alias . '.irhpPermitStock', ':irhpPermitStock'))
            ->setParameter('irhpPermitStock', $irhpPermitStock)
            ->setParameter('proposedStartDate', $proposedStartDate)
            ->setParameter('proposedEndDate', $proposedEndDate);
        if ($irhpPermitWindow !== null) {
            $doctrineQb
                ->andWhere($doctrineQb->expr()->neq($this->alias . '.id', ':irhpPermitWindow'))
                ->setParameter('irhpPermitWindow', $irhpPermitWindow);
        }

        return $doctrineQb->getQuery()->getResult();
    }
}
