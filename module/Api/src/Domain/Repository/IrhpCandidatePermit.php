<?php

namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Permits\IrhpCandidatePermit as Entity;
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
}
