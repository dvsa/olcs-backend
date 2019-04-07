<?php

namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
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
    protected $alias = 'ipw';

    /**
     * Returns an array of IrhpPermitWindow objects that are open as of the specified date and time
     *
     * @param int $irhpPermitStock
     * @param DateTime $currentDateTime
     *
     * @return mixed
     */
    public function fetchOpenWindows(int $irhpPermitStock, DateTime $currentDateTime)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('ipw')
            ->from(Entity::class, 'ipw')
            ->where($qb->expr()->andX(
                $qb->expr()->eq('?1', 'ipw.irhpPermitStock'),
                $qb->expr()->between('?2', 'ipw.startDate', 'ipw.endDate')
            ))
            ->setParameter(1, $irhpPermitStock)
            ->setParameter(2, $currentDateTime);

        $this->getQueryBuilder()
           ->modifyQuery($qb)
           ->withRefdata();

        return $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
    }

    /**
     * Returns the IrhpPermitWindow that was most recently open prior to the specified date and time, or null if there
     * were no windows open prior to the specified date
     *
     * @param int $irhpPermitStock
     * @param DateTime $currentDateTime
     *
     * @return mixed
     */
    public function fetchLastOpenWindow(int $irhpPermitStock, DateTime $currentDateTime)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('ipw')
            ->from(Entity::class, 'ipw')
            ->where($qb->expr()->andX(
                $qb->expr()->eq('?1', 'ipw.irhpPermitStock'),
                $qb->expr()->gt('?2', 'ipw.endDate')
            ))
            ->orderBy('ipw.endDate', 'DESC')
            ->setParameter(1, $irhpPermitStock)
            ->setParameter(2, $currentDateTime)
            ->setMaxResults(1);

        $this->getQueryBuilder()
            ->modifyQuery($qb)
            ->withRefdata();

        return $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
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
     * @param int $irhpPermitStockId
     * @param Query::HYDRATE_OBJECT $hydrationMode
     *
     * @return array
     * @throws NotFoundException
     */
    public function fetchLastOpenWindowByStockId(int $irhpPermitStockId, $hydrationMode = Query::HYDRATE_OBJECT)
    {
        $date = new DateTime();
        $query = $this->getEntityManager()->createQueryBuilder();

        $results = $query->select('ipw')
            ->from(Entity::class, 'ipw')
            ->where($query->expr()->andX(
                $query->expr()->between('?1', 'ipw.startDate', 'ipw.endDate'),
                $query->expr()->eq('ipw.irhpPermitStock', '?2')
            ))
            ->orderBy('ipw.id', 'DESC')
            ->setParameter(1, $date)
            ->setParameter(2, $irhpPermitStockId)
            ->getQuery()
            ->getResult($hydrationMode);

        if (empty($results)) {
            throw new NotFoundException('No window available.');
        }

        return $results[0];
    }

    /**
     * Returns the latest open IrhpPermitWindow for a given IrhpPermitType
     *
     * @param int      $irhpPermitTypeId Irhp Permit Type Id
     * @param DateTime $now              Current datetime
     * @param int      $hydrationMode    Hydration mode
     *
     * @return array
     * @throws NotFoundException
     */
    public function fetchLastOpenWindowByIrhpPermitType(
        int $irhpPermitTypeId,
        DateTime $now,
        $hydrationMode = Query::HYDRATE_OBJECT
    ) {
        $qb = $this->createQueryBuilder();

        $qb
            ->select($this->alias)
            ->innerJoin($this->alias.'.irhpPermitStock', 'ips')
            ->innerJoin('ips.irhpPermitType', 'ipt')
            ->where($qb->expr()->eq('ipt.id', ':irhpPermitTypeId'))
            ->andWhere($qb->expr()->lte($this->alias.'.startDate', ':now'))
            ->andWhere($qb->expr()->gt($this->alias.'.endDate', ':now'))
            ->setParameter('irhpPermitTypeId', $irhpPermitTypeId)
            ->setParameter('now', $now->format(DateTime::ISO8601))
            ->orderBy($this->alias.'.endDate', 'DESC')
            ->setMaxResults(1);

        $results = $qb->getQuery()->getResult($hydrationMode);

        if (empty($results)) {
            throw new NotFoundException('No window available.');
        }

        return $results[0];
    }

    /**
     * Fetch all windows to be closed
     *
     * @param \DateTime $currentDateTime Current datetime
     * @param string    $since           Date since which the query should go to find an expired window
     *
     * @return array
     */
    public function fetchWindowsToBeClosed(\DateTime $currentDateTime, $since = '-1 day')
    {
        $clonedDateTime = clone $currentDateTime;

        $qb = $this->createQueryBuilder();

        $qb
            ->where($qb->expr()->gte($this->alias.'.endDate', ':periodStart'))
            ->andWhere($qb->expr()->lt($this->alias.'.endDate', ':periodEnd'))
            ->setParameter('periodStart', $clonedDateTime->modify($since)->setTime(0, 0, 0)->format(\DateTime::ISO8601))
            ->setParameter('periodEnd', $currentDateTime->format(\DateTime::ISO8601));

        return $qb->getQuery()->getResult();
    }

    /**
     * Fetch all open windows for all the countries provided
     *
     * @param int      $type      Type
     * @param array    $countries List of country ids to check for
     * @param DateTime $now       Now
     *
     * @return array
     */
    public function fetchOpenWindowsByCountry($type, array $countries, DateTime $now)
    {
        $qb = $this->createQueryBuilder();

        $qb
            ->select($this->alias)
            ->distinct()
            ->innerJoin($this->alias.'.irhpPermitStock', 'ips')
            ->innerJoin('ips.irhpPermitType', 'ipt')
            ->innerJoin('ips.country', 'c')
            ->where($qb->expr()->eq('ipt.id', ':type'))
            ->andWhere($qb->expr()->lte($this->alias.'.startDate', ':now'))
            ->andWhere($qb->expr()->gt($this->alias.'.endDate', ':now'))
            ->andWhere($qb->expr()->in('c.id', ':countries'))
            ->setParameter('type', $type)
            ->setParameter('now', $now->format(DateTime::ISO8601))
            ->setParameter('countries', $countries);

        return $qb->getQuery()->getResult();
    }

    /**
     * Fetch all open windows for the specified type
     *
     * @param int      $type Type
     * @param DateTime $now  Now
     *
     * @return array
     */
    public function fetchOpenWindowsByType($type, DateTime $now)
    {
        $qb = $this->createQueryBuilder();

        $qb->select($this->alias)
            ->innerJoin($this->alias.'.irhpPermitStock', 'ips')
            ->innerJoin('ips.irhpPermitType', 'ipt')
            ->where($qb->expr()->eq('ipt.id', ':type'))
            ->andWhere($qb->expr()->lte($this->alias.'.startDate', ':now'))
            ->andWhere($qb->expr()->gt($this->alias.'.endDate', ':now'))
            ->setParameter('type', $type)
            ->setParameter('now', $now->format(DateTime::ISO8601));

        return $qb->getQuery()->getResult();
    }
}
