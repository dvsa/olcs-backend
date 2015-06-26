<?php

/**
 * Vehicle
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Vehicle\Vehicle as Entity;

/**
 * Vehicle
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Vehicle extends AbstractRepository
{
    protected $entity = Entity::class;

    public function fetchLicencesForVrm($vrm)
    {
        $qb = $this->createQueryBuilder();

        $qb->innerJoin('m.licenceVehicles', 'lv');
        $qb->innerJoin('lv.licence', 'l');

        $qb->andWhere(
            $qb->expr()->isNull('lv.removalDate')
        );

        $qb->andWhere(
            $qb->expr()->eq('m.vrm', ':vrm')
        );

        $qb->setParameter('vrm', $vrm);

        $query = $qb->getQuery();

        $query->execute();

        return $query->getResult();
    }
}
