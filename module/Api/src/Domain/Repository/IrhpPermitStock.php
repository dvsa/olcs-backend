<?php

/**
 * IrhpPermitStock
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock as Entity;

/**
 * IrhpPermitStock
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
     *
     * @param string $permitType
     * @param DateTime $date
     *
     * @return array
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getNextIrhpPermitStockByPermitType($permitType, $date)
    {
        return $this->getEntityManager()->createQueryBuilder()
                ->select('ips')
                ->from(Entity::class, 'ips')
                ->innerJoin('ips.irhpPermitType', 'ipt')
                ->where('ips.validFrom >= ?1') //stock starts in future
                ->andWhere('ipt.name = ?2') //Permit Type ECMT
                ->orderBy('ips.validTo', 'ASC')
                ->setParameter(1, $date)
                ->setParameter(2, $permitType)
                ->setMaxResults(1) //There should only ever be one, take the most recent
                ->getQuery()
                ->getOneOrNullResult();
    }

    /**
     * Updates the scoring status of a given stock item
     *
     * @param int $irhpPermitStockId
     * @param string $status
     */
    public function updateStatus($irhpPermitStockId, $status)
    {
        $query = $this->getEntityManager()->createQueryBuilder()
            ->update(Entity::class, 'ips')
            ->set('ips.status', '?1')
            ->where('ips.id = ?2')
            ->setParameter(1, $status)
            ->setParameter(2, $irhpPermitStockId)
            ->getQuery();

        $query->execute();
    }
}
