<?php

/**
 * Transport Manager Application Repository
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
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
     * @param int $applicationId
     *
     * @return array TransportManagerApplication entities
     */
    public function fetchWithContactDetailsByApplication($applicationId)
    {
        $dqb = $this->createQueryBuilder();

        $this->getQueryBuilder()->modifyQuery($dqb)->withRefdata();
        $this->joinTmContactDetails();

        $dqb->andWhere($dqb->expr()->eq($this->alias .'.application', ':applicationId'))
            ->setParameter('applicationId', $applicationId);

        return $dqb->getQuery()->getResult();
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
            $qb->where($qb->expr()->eq('u.id', ':user'))
                ->setParameter('user', $query->getUser());
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

    public function fetchForTransportManager($tmId, $applicationStatuses, $includeDeleted = false)
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
            $statuses = explode(',', $applicationStatuses);
            $conditions = [];
            for ($i = 0; $i < count($statuses); $i++) {
                $conditions[] = 'a.status = :status' . $i;
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
