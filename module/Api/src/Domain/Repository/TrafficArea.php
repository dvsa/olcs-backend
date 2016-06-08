<?php

namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Domain\Exception;
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
     * @return array
     */
    public function getValueOptions()
    {
        $qb = $this->createQueryBuilder();
        $results = $qb->getQuery()->getResult();

        $valueOptions = [];
        /** @var \Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea $result */
        foreach ($results as $result) {
            if ($result->getId() == Entity::NORTHERN_IRELAND_TRAFFIC_AREA_CODE) {
                continue;
            }
            $valueOptions[$result->getId()] = $result->getName();
        }

        asort($valueOptions);

        return $valueOptions;
    }

    public function fetchAll()
    {
        $qb = $this->createQueryBuilder();
        return $qb->getQuery()->getResult();
    }

    public function fetchByTxcName($txcNames)
    {
        /* @var \Doctrine\Orm\QueryBuilder $qb*/
        $qb = $this->createQueryBuilder();
        $this->getQueryBuilder()->modifyQuery($qb);
        $qb->andWhere($qb->expr()->in($this->alias . '.txcName', $txcNames));

        return $qb->getQuery()->execute();
    }

    /**
     * Apply list filters
     *
     * @param string $allowedOperatorLocation
     * @return array
     */
    public function fetchListForNewApplication($allowedOperatorLocation)
    {
        $qb = $this->createQueryBuilder();
        $this->getQueryBuilder()->modifyQuery($qb);
        if ($allowedOperatorLocation !== null) {
            $qb->andWhere($qb->expr()->eq($this->alias . '.isNi', ':isNi'))
                ->setParameter(
                    'isNi', $allowedOperatorLocation === OrganisationEntity::ALLOWED_OPERATOR_LOCATION_NI ? '1' : '0'
                );
        }
        return $qb->getQuery()->getResult();
    }
}
