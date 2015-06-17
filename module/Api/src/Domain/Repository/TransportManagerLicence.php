<?php

/**
 * TransportManagerLicence
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Tm\TransportManagerLicence as Entity;

/**
 * TransportManagerLicence
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TransportManagerLicence extends AbstractRepository
{
    protected $entity = Entity::class;

    public function fetchForLicence($licence = null)
    {
        $qb = $this->createQueryBuilder();

        $qb->where($qb->expr()->eq($this->alias . '.licence', ':licenceId'));
        $qb->setParameter('licenceId', $licence->getId());

        return $qb->getQuery()->getResult();
    }
}
