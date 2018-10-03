<?php

namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitWindow as Entity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock as IrhpPermitStockEntity;
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
     * @return array|null
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
            ->getOneOrNullResult(Query::HYDRATE_ARRAY);
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

    /**
     * @param string $irhpPermitStockType
     * @param DateTime $date
     *
     * @throws NotFoundException
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @return IrhpPermitWindow
     */
    public function fetchLastOpenWindowByPermitType($irhpPermitType, DateTime $date = null)
    {
        if ($date === null) {
            $date = new DateTime();
        }

        $query = $this->getEntityManager()->createQueryBuilder();

        $stock = $query->select('ips')
            ->from(IrhpPermitStockEntity::class, 'ips')
            ->innerJoin('ips.irhpPermitType', 'ipt')
            ->where($query->expr()->andX(
                $query->expr()->lte('ips.validFrom', '?1'),
                $query->expr()->gte('ips.validTo', '?1'),
                $query->expr()->eq('ipt.name', '?2')
            ))
            ->orderBy('ips.validTo', 'ASC')
            ->setParameter(1, $date)
            ->setParameter(2, $irhpPermitType)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if (empty($stock)) {
            throw new NotFoundException('No available stock found.');
        }

        $stockId = $stock->getId();

        $query = $this->getEntityManager()->createQueryBuilder();

        $window = $query->select('ipw')
            ->from(Entity::class, 'ipw')
            ->where($query->expr()->andX(
                $query->expr()->between('?1', 'ipw.startDate', 'ipw.endDate'),
                $query->expr()->eq('ipw.irhpPermitStock', '?2')
            ))
            ->orderBy('ipw.id', 'DESC')
            ->setParameter(1, $date)
            ->setParameter(2, $stockId)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if (empty($window)) {
            throw new NotFoundException('No available window found.');
        }

        return $window;
    }
}
