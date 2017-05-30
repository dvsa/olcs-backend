<?php

namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Tm\TransportManagerApplication as Entity;
use Doctrine\ORM\Query;

/**
 * Transport Manager Application Repository
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class TransportManagerApplication extends AbstractRepository
{
    protected $entity = Entity::class;
    protected $alias = 'tma';

    /**
     * Get a list of transport manager application with contact details
     *
     * @param int $applicationId application id
     *
     * @return array TransportManagerApplication entities
     */
    public function fetchWithContactDetailsByApplication($applicationId)
    {
        $dqb = $this->createQueryBuilder();

        $dqb->leftJoin($this->alias .'.transportManager', 'tm')
            ->leftJoin($this->alias . '.tmApplicationStatus', 'tmas')
            ->leftJoin('tm.homeCd', 'hcd')
            ->leftJoin('hcd.person', 'hp')
            ->select($this->alias . '.id')
            ->addSelect($this->alias . '.action')
            ->addSelect('tm.id as tmid')
            ->addSelect('tmas.id as tmasid, tmas.description as tmasdesc')
            ->addSelect('hcd.emailAddress')
            ->addSelect('hp.birthDate, hp.forename, hp.familyName');

        $dqb->andWhere($dqb->expr()->eq($this->alias .'.application', ':applicationId'))
            ->setParameter('applicationId', $applicationId);

        return $dqb->getQuery()->getResult(Query::HYDRATE_ARRAY);
    }

    /**
     *
     * @param int $tmaId Transport Manager Application ID
     *
     * @return Entity
     * @throws \Dvsa\Olcs\Api\Domain\Exception\NotFoundException
     */
    public function fetchDetails($tmaId)
    {
        $dqb = $this->createQueryBuilder();

        $this->getQueryBuilder()->modifyQuery($dqb)
            ->withRefdata()
            ->with($this->alias .'.application', 'a')
            ->with($this->alias .'.otherLicences', 'ol')
            ->with('ol.role')
            ->with('a.goodsOrPsv', 'gop')
            ->with('a.licence')
            ->with('a.status')
            ->byId($tmaId);

        $this->joinTmContactDetails();

        $results = $dqb->getQuery()->getResult();

        if (empty($results)) {
            throw new \Dvsa\Olcs\Api\Domain\Exception\NotFoundException('Resource not found');
        }

        return $results[0];
    }

    /**
     * Fetch TMA with operating centres
     *
     * @param int $tmaId
     *
     * @return Entity
     * @throws \Dvsa\Olcs\Api\Domain\Exception\NotFoundException
     */
    public function fetchWithOperatingCentres($tmaId)
    {
        $dqb = $this->createQueryBuilder();

        $this->getQueryBuilder()->modifyQuery($dqb)
            ->withRefdata()
            ->with($this->alias .'.operatingCentres', 'oc')
            ->with('oc.address', 'add')
            ->with('add.countryCode')
            ->byId($tmaId);

        $results = $dqb->getQuery()->getResult();

        if (empty($results)) {
            throw new \Dvsa\Olcs\Api\Domain\Exception\NotFoundException('Resource not found');
        }

        return $results[0];
    }

    /**
     * Join Trasport Manager, Contact Details and Person entities to the query
     */
    protected function joinTmContactDetails()
    {
        $this->getQueryBuilder()->with($this->alias .'.transportManager', 'tm')
            ->with('tm.homeCd', 'hcd')
            ->with('hcd.address', 'hadd')
            ->with('hadd.countryCode')
            ->with('hcd.person', 'hp')
            ->with('tm.workCd', 'wcd')
            ->with('wcd.address', 'wadd')
            ->with('wadd.countryCode');
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $qb
     * @param \Dvsa\Olcs\Transfer\Query\QueryInterface $query
     */
    protected function applyListFilters(\Doctrine\ORM\QueryBuilder $qb, \Dvsa\Olcs\Transfer\Query\QueryInterface $query)
    {
        // if user param exists then add where clauses
        if ($query->getUser()) {
            $qb->join('tma.transportManager', 'tm');
            $qb->join('tm.users', 'u');
            $qb->andWhere($qb->expr()->eq('u.id', ':user'))
                ->setParameter('user', $query->getUser());
        }

        if ($query->getApplication()) {
            $qb->andWhere($qb->expr()->eq('tma.application', ':application'))
                ->setParameter('application', $query->getApplication());
        }

        if ($query->getTransportManager()) {
            $qb->andWhere($qb->expr()->eq('tma.transportManager', ':transportManager'))
                ->setParameter('transportManager', $query->getTransportManager());
        }

        if ($query->getAppStatuses()) {
            $qb->andWhere($qb->expr()->in('a.status', ':appStatuses'))
                ->setParameter('appStatuses', $query->getAppStatuses());
        }

        if ($query->getFilterByOrgUser() && $query->getUser()) {
            $qb->join('l.organisation', 'o');
            $qb->join('o.organisationUsers', 'ou');
            $qb->join('ou.user', 'ouu');
            $qb->andWhere($qb->expr()->eq('ouu.id', ':orgUsersUser'))
                ->setParameter('orgUsersUser', $query->getUser());
        }
    }

    /**
     * Add joins
     *
     * @param \Doctrine\ORM\QueryBuilder $qb
     */
    protected function applyListJoins(\Doctrine\ORM\QueryBuilder $qb)
    {
        $this->getQueryBuilder()
            ->with('application', 'a')
            ->with('a.licence', 'l');
    }

    public function fetchForTransportManager($tmId, array $applicationStatuses = null, $includeDeleted = false)
    {
        $qb = $this->createQueryBuilder();

        $this->getQueryBuilder()
            ->modifyQuery($qb)
            ->with('tmType', 'tmt')
            ->with('application', 'a')
            ->with('a.licence', 'al')
            ->with('al.organisation', 'alo')
            ->with('a.status', 'ast')
            ->with('transportManager', 'tm')
            ->with('operatingCentres', 'oc')
            ->with('tmApplicationStatus', 'tmast');

        $qb->where($qb->expr()->eq($this->alias . '.transportManager', ':transportManager'));
        $qb->setParameter('transportManager', $tmId);

        if (!$includeDeleted) {
            $qb->andWhere($qb->expr()->neq($this->alias . '.action', ':action'));
            $qb->setParameter('action', 'D');
        }

        if ($applicationStatuses !== null) {
            $qb->andWhere($qb->expr()->in('a.status', $applicationStatuses));
        }

        return $qb->getQuery()->getResult();
    }

    public function fetchByTmAndApplication($tmId, $applicationId, $ignoreDeleted = false)
    {
        $qb = $this->createQueryBuilder();

        $qb->andWhere($qb->expr()->eq($this->alias .'.transportManager', ':tmId'))
            ->setParameter('tmId', $tmId);
        $qb->andWhere($qb->expr()->eq($this->alias .'.application', ':applicationId'))
            ->setParameter('applicationId', $applicationId);
        if ($ignoreDeleted) {
            $qb->andWhere($qb->expr()->neq($this->alias .'.action', ':action'))
                ->setParameter('action', 'D');
        }

        return $qb->getQuery()->getResult();
    }

    public function fetchForResponsibilities($id)
    {
        $qb = $this->createQueryBuilder();

        $this->getQueryBuilder()
            ->modifyQuery($qb)
            ->with('application', 'a')
            ->with('tmType', 'tmty')
            ->with('a.licence', 'al')
            ->with('al.organisation', 'alo')
            ->with('a.status', 'ast')
            ->with('transportManager', 'tm')
            ->with('tm.tmType', 'tmt')
            ->with('operatingCentres', 'oc')
            ->with('tmApplicationStatus', 'tmast')
            ->byId($id);

        return $qb->getQuery()->getSingleResult();
    }
}
