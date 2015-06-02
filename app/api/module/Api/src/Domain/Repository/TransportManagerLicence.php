<?php

/**
 * Transport Manager Licence Repository
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Tm\TransportManagerLicence as Entity;

/**
 * Transport Manager Licence Repository
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class TransportManagerLicence extends AbstractRepository
{
    protected $entity = Entity::class;
    protected $alias = 'tml';

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
        $dqb->join('tml.transportManager', 'tm')->addSelect('tm')
            ->join('tm.homeCd', 'hcd')->addSelect('hcd')
            ->join('hcd.person', 'p')->addSelect('p');

        $dqb->andWhere($dqb->expr()->eq('tml.licence', ':licenceId'))
            ->setParameter('licenceId', $licenceId);

        return $dqb->getQuery()->getResult();
    }
}
