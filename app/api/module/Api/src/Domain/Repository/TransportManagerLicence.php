<?php

/**
 * TransportManagerLicence.php
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Tm\TransportManagerLicence as Entity;

/**
 * TransportManagerLicence repository
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Mat Evans <mat.evans@valtech.co.uk>
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
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

    public function fetchForTransportManager($tmId, $licenceStatuses)
    {
        $qb = $this->createQueryBuilder();

        $this->getQueryBuilder()
            ->modifyQuery($qb)
            ->with('tmType', 'tmt')
            ->with('licence', 'l')
            ->with('l.organisation', 'lo')
            ->with('l.status', 'ls')
            ->with('transportManager', 'tm')
            ->with('operatingCentres', 'oc');

        $qb->where($qb->expr()->eq($this->alias . '.transportManager', ':transportManager'));
        $qb->setParameter('transportManager', $tmId);

        if ($licenceStatuses !== null) {
            $statuses = explode(',', $licenceStatuses);
            $conditions = [];
            for ($i = 0; $i < count($statuses); $i++) {
                $conditions[] = 'l.status = :status' . $i;
            }
            $orX = $qb->expr()->orX();
            $orX->addMultiple($conditions);
            $qb->andWhere($orX);
            for ($i = 0; $i < count($statuses); $i++) {
                $qb->setParameter('status' . $i, $statuses[$i]);
            }
        }
        return $qb->getQuery()->getResult();
    }

    public function fetchForResponsibilities($id)
    {
        $qb = $this->createQueryBuilder();

        $this->getQueryBuilder()
            ->modifyQuery($qb)
            ->with('licence', 'l')
            ->with('l.organisation', 'lo')
            ->with('l.status', 'lst')
            ->with('transportManager', 'tm')
            ->with('tm.tmType', 'tmty')
            ->with('tmType', 'tmt')
            ->with('operatingCentres', 'oc')
            ->byId($id);

        return $qb->getQuery()->getSingleResult();
    }
}
