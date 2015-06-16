<?php

/**
 * PublicationLink
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Entity\Publication\PublicationLink as Entity;

/**
 * PublicationLink
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class PublicationLink extends AbstractRepository
{
    protected $entity = Entity::class;

    public function fetchByBusRegId($busRegId)
    {
        $qb = $this->createQueryBuilder();

        $qb->andWhere(
            $qb->expr()->eq('busReg', ':busReg')
        )->setParameter('busReg', $busRegId);

        return $qb->getQuery()->getResult(Query::HYDRATE_OBJECT);
    }
}
