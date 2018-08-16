<?php

/**
 * Inspection Request
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */

namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\EnforcementArea\EnforcementArea;
use Dvsa\Olcs\Api\Entity\Inspection\InspectionRequest as Entity;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Doctrine\ORM\QueryBuilder;

/**
 * Inspection Request
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class InspectionRequest extends AbstractRepository
{
    protected $entity = Entity::class;

    public function fetchForInspectionRequest($id)
    {
        $qb = $this->createQueryBuilder();
          $this->getQueryBuilder()->modifyQuery($qb)
            ->with('licence', 'l')
            ->with('l.licenceType', 'lt')
            ->with('l.organisation', 'l_o')
            ->with('l_o.organisationPersons', 'l_o_p')
            ->with('l_o_p.person', 'l_o_p_p')
            ->with('l_o.tradingNames', 'l_o_tn')
            ->with('l.correspondenceCd', 'l_ccd')
            ->with('l_ccd.address', 'l_ccd_a')
            ->with('l_ccd.phoneContacts', 'l_ccd_pc')
            ->with('l_ccd_pc.phoneContactType', 'l_ccd_pc_pct')
            ->with('l.enforcementArea', 'l_ea')
            ->with('operatingCentre', 'oc')
            ->with('oc.address', 'oc_a')
            ->with('application', 'a')
            ->with('a.licence', 'a_l')
            ->with('a.licenceType', 'a_lt')
            ->withRefData()
            ->byId($id);

        return $qb->getQuery()->getSingleResult(Query::HYDRATE_ARRAY);
    }

    /**
     * Fetch count of licence operating centres linked to the inspection request
     *
     * @param int $inspectionRequestId Inspection Request ID
     *
     * @return int
     */
    public function fetchLicenceOperatingCentreCount($inspectionRequestId)
    {
        $qb = $this->createQueryBuilder();
        $this->getQueryBuilder()->modifyQuery($qb)
            ->with('licence', 'l')
            ->with('l.operatingCentres', 'l_oc')
            ->with('l_oc.operatingCentre', 'l_oc_oc')
            ->byId($inspectionRequestId);
        $qb->select('COUNT(' . $this->alias . ')');

        return $qb->getQuery()->getSingleResult(Query::HYDRATE_SINGLE_SCALAR);
    }

    /**
     * @param QueryBuilder             $qb
     * @param InspectionRequestListDTO $query
     */
    protected function applyListFilters(QueryBuilder $qb, QueryInterface $query)
    {
        $qb->andWhere($qb->expr()->eq($this->alias . '.licence', ':licence'));
        $qb->setParameter('licence', $query->getLicence());
    }



    protected function applyListJoins(QueryBuilder $qb)
    {
        $this->getQueryBuilder()->modifyQuery($qb)
            ->with('licence', 'l')
            ->with('application', 'a');
    }

    public function fetchPage(QueryInterface $query, $licenceId)
    {
        $qb = $this->createQueryBuilder();
        $this->buildDefaultListQuery($qb, $query);
        $qb->andWhere($qb->expr()->eq($this->alias . '.licence', ':licence'));
        $qb->setParameter('licence', $licenceId);

        $this->getQueryBuilder()->modifyQuery($qb)
            ->with('licence', 'l')
            ->with('application', 'a');

        return [
            'result' => $this->fetchPaginatedList($qb, Query::HYDRATE_OBJECT),
            'count' => $this->fetchPaginatedCount($qb)
        ];
    }
}
