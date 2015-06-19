<?php

/**
<<<<<<< HEAD
 * TransportManagerLicence
 *
 * @author Rob Caiger <rob@clocal.co.uk>
=======
 * Transport Manager Licence Repository
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
>>>>>>> c37d244738f5c255e48d5756c36575beeb5d7337
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Tm\TransportManagerLicence as Entity;

/**
 * TransportManagerLicence
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class TransportManagerLicence extends AbstractRepository
{
    protected $alias = 'tml';

    protected $entity = Entity::class;

    public function fetchForLicence($licence = null)
    {
        $qb = $this->createQueryBuilder();

        $qb->where($qb->expr()->eq($this->alias . '.licence', ':licenceId'));
        $qb->setParameter('licenceId', $licence->getId());

        return $qb->getQuery()->getResult();
    }

    /**
     * Get a list of transport manager licences with contact details
     *
     * @param int $licenceId
     *
     * @return array TransportManagerLicence entities
     */
    public function fetchWithContactDetailsByLicence($licenceId)
    {
        $dqb = $this->createQueryBuilder();

        $this->getQueryBuilder()->modifyQuery($dqb)->withRefdata();
        $dqb->join($this->alias .'.transportManager', 'tm')->addSelect('tm')
            ->join('tm.homeCd', 'hcd')->addSelect('hcd')
            ->join('hcd.person', 'p')->addSelect('p');

        $dqb->andWhere($dqb->expr()->eq($this->alias .'.licence', ':licenceId'))
            ->setParameter('licenceId', $licenceId);

        return $dqb->getQuery()->getResult();
    }
}
