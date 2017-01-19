<?php

/**
 * TransportManagerLicence.php
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Tm\TransportManagerLicence as Entity;
use Doctrine\ORM\Query;

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

    /**
     * @param int $licenceId
     */
    public function fetchForLicence($licenceId)
    {
        $qb = $this->createQueryBuilder();

        $qb->where($qb->expr()->eq($this->alias . '.licence', ':licenceId'));
        $qb->setParameter('licenceId', $licenceId);

        return $qb->getQuery()->getResult();
    }

    /**
     * Get a list of transport manager licences with contact details
     *
     * @param int $licenceId licence id
     *
     * @return array TransportManagerLicence entities
     */
    public function fetchWithContactDetailsByLicence($licenceId)
    {
        $dqb = $this->createQueryBuilder();

        $dqb->andWhere($dqb->expr()->eq($this->alias .'.licence', ':licenceId'))
            ->setParameter('licenceId', $licenceId);

        $dqb->join($this->alias .'.transportManager', 'tm')
            ->join('tm.homeCd', 'hcd')
            ->join('hcd.person', 'p')
            ->select($this->alias . '.id')
            ->addSelect('tm.id as tmid')
            ->addSelect('p.birthDate, p.forename, p.familyName')
            ->addSelect('hcd.emailAddress');

        return $dqb->getQuery()->getResult(Query::HYDRATE_ARRAY);
    }

    public function fetchForTransportManager($tmId, array $licenceStatuses = null)
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
            $qb->andWhere($qb->expr()->in('l.status', $licenceStatuses));
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

    public function fetchByTmAndLicence($tmId, $licenceId)
    {
        $qb = $this->createQueryBuilder();

        $qb->andWhere($qb->expr()->eq($this->alias .'.transportManager', ':tmId'))
            ->setParameter('tmId', $tmId);
        $qb->andWhere($qb->expr()->eq($this->alias .'.licence', ':licenceId'))
            ->setParameter('licenceId', $licenceId);

        return $qb->getQuery()->getResult();
    }

    protected function applyListFilters(\Doctrine\ORM\QueryBuilder $qb, \Dvsa\Olcs\Transfer\Query\QueryInterface $query)
    {
        if ($query->getLicence()) {
            $qb->where($qb->expr()->eq('tml.licence', ':licence'))
                ->setParameter('licence', $query->getLicence());
        }

        if ($query->getTransportManager()) {
            $qb->where($qb->expr()->eq('tml.transportManager', ':transportManager'))
                ->setParameter('transportManager', $query->getTransportManager());
        }
    }

    public function fetchByLicence($licenceId)
    {
        $qb = $this->createQueryBuilder();

        $this->getQueryBuilder()
            ->modifyQuery($qb)
            ->with('licence', 'l')
            ->with('l.applications', 'la')
            ->with('la.licenceType')
            ->with('transportManager', 'tm');

        $qb->where($qb->expr()->eq('tml.licence', ':licence'))
            ->setParameter('licence', $licenceId);

        return $qb->getQuery()->getResult();
    }
}
