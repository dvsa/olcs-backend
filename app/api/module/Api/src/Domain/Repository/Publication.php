<?php

/**
 * Publication
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Entity\Publication\Publication as Entity;
use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Publication
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Publication extends AbstractRepository
{
    protected $entity = Entity::class;

    const PUB_NEW_STATUS = 'pub_s_new';

    public function fetchLatestForTrafficAreaAndType($trafficArea, $pubType)
    {
        $qb = $this->createQueryBuilder();

        $qb->andWhere(
            $qb->expr()->eq($this->alias . '.trafficArea', ':trafficArea')
        )->setParameter('trafficArea', $trafficArea);

        $qb->andWhere(
            $qb->expr()->eq($this->alias . '.pubType', ':pubType')
        )->setParameter('pubType', $pubType);

        $qb->andWhere(
            $qb->expr()->eq($this->alias . '.pubStatus', ':pubStatus')
        )->setParameter('pubStatus', self::PUB_NEW_STATUS);

        $this->getQueryBuilder()->modifyQuery($qb);

        $result = $qb->getQuery()->getResult(Query::HYDRATE_OBJECT);

        if (empty($result)) {
            throw new NotFoundException('Resource not found');
        }

        return $result[0];
    }

    /**
     * Applies filters
     * @param QueryBuilder $qb
     * @param QueryInterface $query
     */
    protected function applyListFilters(QueryBuilder $qb, QueryInterface $query)
    {
        if (method_exists($query, 'getPubStatus')) {
            $qb->andWhere($qb->expr()->eq($this->alias . '.pubStatus', ':byPubStatus'))
                ->setParameter('byPubStatus', $query->getPubStatus());
        }

        if (method_exists($query, 'getPubType')) {
            $qb->andWhere($qb->expr()->eq($this->alias . '.pi', ':byPubType'))
                ->setParameter('byPubType', $query->getPubType());
        }

        if (method_exists($query, 'getTrafficArea')) {
            $qb->andWhere($qb->expr()->eq($this->alias . '.trafficArea', ':byTrafficArea'))
                ->setParameter('byTrafficArea', $query->getTrafficArea());
        }
    }
}
