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
}
