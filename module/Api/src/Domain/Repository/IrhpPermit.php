<?php

namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Permits\IrhpPermit as Entity;
use Doctrine\ORM\Query;

/**
 * IRHP Permit
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class IrhpPermit extends AbstractRepository
{
    protected $entity = Entity::class;

    /**
     * Returns the count of permits in the specified stock
     *
     * @param int $stockId
     *
     * @return int
     */
    public function getPermitCount($stockId)
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('count(ip.id)')
            ->from(Entity::class, 'ip')
            ->innerJoin('ip.irhpPermitRange', 'ipr')
            ->where('IDENTITY(ipr.irhpPermitStock) = ?1')
            ->setParameter(1, $stockId)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Get permits by ECMT application id
     *
     * @param \Dvsa\Olcs\Transfer\Query\Permits\ValidEcmtPermits $query Query
     *
     * @return array
     */
    public function fetchByEcmtApplicationPaginated($query)
    {
        $qb = $this->createQueryBuilder();
        $this->getQueryBuilder()->modifyQuery($qb)
            ->withRefdata()
            ->with('irhpPermitApplication', 'ipa')
            ->with('ipa.ecmtPermitApplication', 'epa')
            ->paginate($query->getPage(), $query->getLimit());

        $qb->andWhere($qb->expr()->eq('epa.id', ':ecmtId'))
            ->setParameter('ecmtId', $query->getId());

        return $this->fetchPaginatedList($qb, Query::HYDRATE_OBJECT);
    }

}
