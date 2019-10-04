<?php

namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Permits\IrhpCandidatePermit as Entity;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Transfer\Query\Permits\UnpaidEcmtPermits;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * IRHP Candidate Permit
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class IrhpCandidatePermit extends AbstractRepository
{
    protected $entity = Entity::class;

    /**
     * Apply List Filters
     *
     * @param QueryBuilder   $qb    Doctrine Query Builder
     * @param QueryInterface $query Http Query
     *
     * @return void
     */
    protected function applyListFilters(QueryBuilder $qb, QueryInterface $query)
    {
        if ($query instanceof UnpaidEcmtPermits) {
            $qb->andWhere($qb->expr()->eq($this->alias . '.successful', ':successful'))
                ->setParameter('successful', true);
            $qb->andWhere($qb->expr()->eq('epa.status', ':status'))
                ->setParameter('status', $query->getStatus());
            $qb->andWhere($qb->expr()->eq('ipa.ecmtPermitApplication', ':ecmtId'))
                ->setParameter('ecmtId', $query->getId());
        }
        if (method_exists($query, 'getEcmtPermitApplication')) {
            $qb->andWhere($qb->expr()->eq('epa.id', ':ecmtId'))
                ->setParameter('ecmtId', $query->getEcmtPermitApplication());
        }
    }

    /**
     * Add List Joins
     *
     * @param QueryBuilder $qb Doctrine Query Builder
     *
     * @return void
     */
    protected function applyListJoins(QueryBuilder $qb)
    {
        $this->getQueryBuilder()->modifyQuery($qb)
            ->with('irhpPermitApplication', 'ipa')
            ->with('ipa.ecmtPermitApplication', 'epa');
    }

    /**
     * Retrieves a partial list of column values for the scoring report
     *
     * @param int $irhpPermitStockId the Id of the IrhpPermitStock that the scoring will be for
     *
     * @return array
     */
    public function fetchScoringReport($irhpPermitStockId)
    {
        $columns = [
            'icp.id as candidatePermitId',
            'epa.id as applicationId',
            'o.name as organisationName',
            'icp.applicationScore as candidatePermitApplicationScore',
            'icp.intensityOfUse as candidatePermitIntensityOfUse',
            'icp.randomFactor as candidatePermitRandomFactor',
            'icp.randomizedScore as candidatePermitRandomizedScore',
            'IDENTITY(icp.requestedEmissionsCategory) as candidatePermitRequestedEmissionsCategory',
            'IDENTITY(icp.assignedEmissionsCategory) as candidatePermitAssignedEmissionsCategory',
            'IDENTITY(epa.internationalJourneys) as applicationInternationalJourneys',
            's.name as applicationSectorName',
            'l.licNo as licenceNo',
            'ta.id as trafficAreaId',
            'ta.name as trafficAreaName',
            'icp.successful as candidatePermitSuccessful',
            'IDENTITY(icp.irhpPermitRange) as candidatePermitRangeId'
        ];

        return $this->getEntityManager()->createQueryBuilder()
            ->select(implode(', ', $columns))
            ->from(Entity::class, 'icp')
            ->innerJoin('icp.irhpPermitApplication', 'ipa')
            ->innerJoin('ipa.irhpPermitWindow', 'ipw')
            ->innerJoin('ipa.ecmtPermitApplication', 'epa')
            ->innerJoin('epa.licence', 'l')
            ->innerJoin('epa.sectors', 's')
            ->innerJoin('l.trafficArea', 'ta')
            ->innerJoin('l.organisation', 'o')
            ->where('IDENTITY(ipw.irhpPermitStock) = ?1')
            ->andWhere('epa.status = ?2')
            ->andWhere('epa.inScope = 1')
            ->setParameter(1, $irhpPermitStockId)
            ->setParameter(2, EcmtPermitApplication::STATUS_UNDER_CONSIDERATION)
            ->getQuery()
            ->getScalarResult();
    }
}
