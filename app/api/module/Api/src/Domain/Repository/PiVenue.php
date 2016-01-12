<?php

/**
 * PiVenue Repository
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Pi\PiVenue as Entity;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * PiVenue Repository
 */
class PiVenue extends AbstractRepository
{
    protected $entity = Entity::class;

    /**
     * Applies a trafficArea filter
     * @param QueryBuilder $qb
     * @param QueryInterface $query
     */
    protected function applyListFilters(QueryBuilder $qb, QueryInterface $query)
    {
        $qb->andWhere($qb->expr()->eq($this->alias . '.trafficArea', ':trafficArea'))
            ->setParameter('trafficArea', $query->getTrafficArea());
        $qb->orderBy($this->alias . '.name', 'ASC');
    }
}
