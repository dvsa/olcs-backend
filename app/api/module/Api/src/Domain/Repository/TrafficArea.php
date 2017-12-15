<?php

namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Organisation\Organisation as OrganisationEntity;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea as Entity;

/**
 * Traffic Area
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class TrafficArea extends AbstractRepository
{
    protected $entity = Entity::class;

    /**
     * Get value options (suitable for UI) for traffic areas, optionally for a given allowed location. In the case of
     * NI we allow selection of any traffic area rather than on NI areas due to a bug OLCS-18206) which could have lead
     * to mismatches in traffic areas.
     *
     * @param string|null $allowedOperatorLocation allowed operator location
     *
     * @see OrganisationEntity::getAllowedOperatorLocation
     *
     * @return array
     */
    public function getValueOptions($allowedOperatorLocation = null)
    {
        $qb = $this->createQueryBuilder();

        $qb->orderBy($this->alias . '.name');

        if ($allowedOperatorLocation != OrganisationEntity::ALLOWED_OPERATOR_LOCATION_NI) {
            $qb->andWhere($qb->expr()->eq($this->alias . '.isNi', ':isNi'))->setParameter('isNi', 0);
        }

        $results = $qb->getQuery()->getResult();

        $valueOptions = [];
        /** @var \Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea $result */
        foreach ($results as $result) {
            $valueOptions[$result->getId()] = $result->getName();
        }

        return $valueOptions;
    }

    public function fetchAll()
    {
        $qb = $this->createQueryBuilder();
        return $qb->getQuery()->getResult();
    }

    public function fetchByTxcName($txcNames)
    {
        /* @var \Doctrine\Orm\QueryBuilder $qb */
        $qb = $this->createQueryBuilder();
        $this->getQueryBuilder()->modifyQuery($qb);
        $qb->andWhere($qb->expr()->in($this->alias . '.txcName', $txcNames));

        return $qb->getQuery()->execute();
    }

    /**
     * Apply list filters
     *
     * @param string $allowedOperatorLocation
     *
     * @return array
     */
    public function fetchListForNewApplication($allowedOperatorLocation)
    {
        $qb = $this->createQueryBuilder();
        $this->getQueryBuilder()->modifyQuery($qb);
        if ($allowedOperatorLocation !== null) {
            $qb->andWhere($qb->expr()->eq($this->alias . '.isNi', ':isNi'))
                ->setParameter(
                    'isNi',
                    $allowedOperatorLocation === OrganisationEntity::ALLOWED_OPERATOR_LOCATION_NI ? '1' : '0'
                );
        }
        return $qb->getQuery()->getResult();
    }
}
