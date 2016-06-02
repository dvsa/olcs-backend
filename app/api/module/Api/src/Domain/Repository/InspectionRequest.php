<?php

/**
 * Inspection Request
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

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
            ->with('l_o.licences', 'l_o_l')
            ->with('l.workshops', 'l_w')
            ->with('l_w.contactDetails', 'l_w_cd')
            ->with('l_w_cd.address', 'l_w_cd_a')
            ->with('l.operatingCentres', 'l_oc')
            ->with('l_oc.operatingCentre', 'l_oc_oc')
            ->with('l.correspondenceCd', 'l_ccd')
            ->with('l_ccd.address', 'l_ccd_a')
            ->with('l_ccd.phoneContacts', 'l_ccd_pc')
            ->with('l_ccd_pc.phoneContactType', 'l_ccd_pc_pct')
            ->with('l.tmLicences', 'l_tml')
            ->with('l_tml.transportManager', 'l_tml_tm')
            ->with('l_tml_tm.homeCd', 'l_tml_tm_hcd')
            ->with('l_tml_tm_hcd.person', 'l_tml_tm_hcd_p')
            ->with('l.enforcementArea', 'l_ea')
            ->with('operatingCentre', 'oc')
            ->with('oc.address', 'oc_a')
            ->with('application', 'a')
            ->with('a.licence', 'a_l')
            ->with('a.licenceType', 'a_lt')
            ->with('a.operatingCentres', 'a_oc')
            ->with('a_oc.operatingCentre', 'a_oc_oc')
            ->with('a_oc_oc.address', 'a_oc_oc_a')
            ->withRefData()
            ->byId($id);
        return $qb->getQuery()->getSingleResult(Query::HYDRATE_ARRAY);
    }

    /**
     * @param QueryBuilder $qb
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
            'count'  => $this->fetchPaginatedCount($qb)
        ];
    }
}
