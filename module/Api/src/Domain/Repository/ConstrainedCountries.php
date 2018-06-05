<?php

/**
 * Constrained Countries
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\EcmtCountryConstraintLink as Entity;

/**
 * Constrained Countries
 */
class ConstrainedCountries extends AbstractRepository
{
    protected $entity = Entity::class;


    /**
     * Check for constraints
     *
     * @param string $countryId
     * @return bool
     */
    public function existsByCountryId($countryId)
    {
        $qb = $this->createQueryBuilder();

        $qb->where($qb->expr()->eq($this->alias .'.country', ':countryId'));
        $qb->setParameter('countryId', $countryId);
        $qb->setMaxResults(1);

        return count($qb->getQuery()->getResult()) === 1;
    }
}
