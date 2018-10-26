<?php

namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitRange as Entity;

/**
 * IRHP Permit Range
 */
class IrhpPermitRange extends AbstractRepository
{
    protected $entity = Entity::class;

    /**
     * Returns the number of possible permit numbers across all ranges in the specified stockId. Will return NULL if
     * no ranges were found against the specified stockId
     *
     * @param int $stockId
     *
     * @return int|null
     */
    public function getCombinedRangeSize($stockId)
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('SUM((ipr.toNo - ipr.fromNo) + 1)')
            ->from(Entity::class, 'ipr')
            ->where('ipr.ssReserve = false')
            ->andWhere('ipr.lostReplacement = false')
            ->andWhere('IDENTITY(ipr.irhpPermitStock) = ?1')
            ->setParameter(1, $stockId)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Returns all ranges in the specified stockId.
     *
     * @param int $stockId
     *
     * @return array
     */
    public function getRanges($stockId)
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('r')
            ->from(Entity::class, 'r')
            ->andWhere('IDENTITY(r.irhpPermitStock) = ?1')
            ->setParameter(1, $stockId)
            ->getQuery()
            ->getResult();
    }

    /*
     * Fetch Ranges by Permit Stock ID
     *
     * @param int $irhpPermitStockId
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
     * Fetch Overlapping Ranges by Permit Stock ID, proposed Start value and proposed End value
     *
     * @param IrhpPermitStock $irhpPermitStock
     * @param int $proposedStartValue
     * @param int $proposedEndValue
     * @param null $irhpPermitRange
     * @return array
     */
    public function findOverlappingRangesByType($irhpPermitStock, $proposedStartValue, $proposedEndValue, $irhpPermitRange = null)
    {
        $doctrineQb = $this->createQueryBuilder();
        $doctrineQb
            ->orWhere($doctrineQb->expr()->between($this->alias . '.fromNo', ':fromNo', ':toNo'))
            ->orWhere($doctrineQb->expr()->between($this->alias . '.toNo', ':fromNo', ':toNo'))
            ->orWhere($doctrineQb->expr()->between(':fromNo', $this->alias . '.fromNo', $this->alias .'.toNo'))
            ->andWhere($doctrineQb->expr()->eq($this->alias . '.irhpPermitStock', ':irhpPermitStock'))
            ->setParameter('irhpPermitStock', $irhpPermitStock)
            ->setParameter('fromNo', $proposedStartValue)
            ->setParameter('toNo', $proposedEndValue);

        if ($irhpPermitRange !== null) {
            $doctrineQb
                ->andWhere($doctrineQb->expr()->neq($this->alias . '.id', ':irhpPermitRange'))
                ->setParameter('irhpPermitRange', $irhpPermitRange);
        }

        return $doctrineQb->getQuery()->getResult();
    }

    /**
     * Returns all non-reserved, non-replacement ranges in the specified stock
     *
     * @param int $stockId
     *
     * @return array
     */
    public function getByStockId($stockId)
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('ipr')
            ->from(Entity::class, 'ipr')
            ->where('ipr.ssReserve = false')
            ->andWhere('ipr.lostReplacement = false')
            ->andWhere('IDENTITY(ipr.irhpPermitStock) = ?1')
            ->setParameter(1, $stockId)
            ->getQuery()
            ->getResult();
    }
}
