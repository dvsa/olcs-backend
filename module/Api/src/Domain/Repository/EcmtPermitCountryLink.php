<?php

/**
 * ECMT Permit Country Link
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\EcmtPermitCountryLink as Entity;

/**
 * ECMT Permit Country Link
 */
class EcmtPermitCountryLink extends AbstractRepository
{
    protected $entity = Entity::class;


    /**
     * get countries by permit id
     *
     * @param string $ecmtPermitId
     * @return array
     */
    public function getByPermitId($ecmtPermitId)
    {
        $qb = $this->createQueryBuilder();

        $qb->where($qb->expr()->eq($this->alias .'.ecmtPermit', ':ecmtPermitId'));
        $qb->setParameter('ecmtPermitId', $ecmtPermitId);

        return $qb->getQuery()->getResult();
    }
}
