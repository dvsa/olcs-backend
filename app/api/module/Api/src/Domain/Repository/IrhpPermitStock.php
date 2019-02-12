<?php

/**
 * IrhpPermitStock
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
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
                $query->expr()->lte('?1', 'ips.validTo'),
                $query->expr()->eq('ipt.name', '?2')
            ))
            ->setParameter(1, $date)
            ->setParameter(2, $permitType)
            ->orderBy('ips.validFrom', 'ASC')
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
     * @param int    $irhpPermitTypeId Irhp Permit Type Id
     * @param string $countryId        Country Id
     *
     * @return array
     */
    public function fetchReadyToPrint($irhpPermitTypeId, $countryId = null)
    {
        $qb = $this->createQueryBuilder();

        $qb
            ->select($this->alias)
            ->distinct()
            ->innerJoin($this->alias . '.irhpPermitRanges', 'ipr')
            ->innerJoin('ipr.irhpPermits', 'ip')
            ->where($qb->expr()->in('ip.status', ':statuses'))
            ->andWhere($this->alias . '.irhpPermitType = :irhpPermitTypeId')
            ->setParameter('statuses', IrhpPermitEntity::$readyToPrintStatuses)
            ->setParameter('irhpPermitTypeId', $irhpPermitTypeId);

        if (!empty($countryId)) {
            $qb
                ->andWhere($this->alias . '.country = :countryId')
                ->setParameter('countryId', $countryId);
        }

        $qb->orderBy($this->alias . '.validFrom', 'DESC');

        return $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
    }

    /**
     * Returns the count of permit stocks for a type and validity period
     *
     * @param int $permitTypeId
     * @param string $validFrom
     * @param string $validTo
     * @return int
     */
    public function getPermitStockCountByTypeDate($permitTypeId, $validFrom, $validTo)
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('count(ips.id)')
            ->from(Entity::class, 'ips')
            ->where('ips.irhpPermitType = ?1')
            ->andWhere('ips.validFrom = ?2')
            ->andWhere('ips.validTo = ?3')
            ->setParameter(1, $permitTypeId)
            ->setParameter(2, $validFrom)
            ->setParameter(3, $validTo)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @param int $irhpPermitType
     * @return array
     */
    public function fetchByIrhpPermitType(int $irhpPermitType)
    {
        return $this->fetchByX('irhpPermitType', [$irhpPermitType]);
    }

    /**
     * @return array
     */
    public function fetchAll()
    {
        $qb = $this->createQueryBuilder();
        return $qb->getQuery()->getResult();
    }
}
