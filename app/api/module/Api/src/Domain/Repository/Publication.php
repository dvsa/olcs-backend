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
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Publication
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Publication extends AbstractRepository
{
    protected $entity = Entity::class;

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
        )->setParameter('pubStatus', Entity::PUB_NEW_STATUS);

        $this->getQueryBuilder()->modifyQuery($qb);

        $result = $qb->getQuery()->getResult(Query::HYDRATE_OBJECT);

        if (empty($result)) {
            throw new NotFoundException('Resource not found');
        }

        return $result[0];
    }

    public function fetchPendingList(QueryInterface $query)
    {
        $qb = $this->createQueryBuilder();

        $qb->andWhere(
            $qb->expr()->in($this->alias . '.pubStatus', ':pubStatus')
        )->setParameter('pubStatus', [Entity::PUB_NEW_STATUS, Entity::PUB_GENERATED_STATUS]);

        $this->buildDefaultListQuery($qb, $query);

        $this->getQueryBuilder()->modifyQuery($qb);

        return $qb->getQuery()->getResult();
    }
}
