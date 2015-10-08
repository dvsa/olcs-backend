<?php

namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Ebsr\EbsrSubmission as Entity;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * EbsrSubmission
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class EbsrSubmission extends AbstractRepository
{
    protected $entity = Entity::class;

    /**
     * Fetch a list for an organisation
     *
     * @param int|\Dvsa\Olcs\Api\Entity\Organisation\Organisation $organisation
     *
     * @return array
     */
    public function fetchByOrganisation($organisation)
    {
        $doctrineQb = $this->createQueryBuilder();

        $doctrineQb->andWhere($doctrineQb->expr()->eq($this->alias . '.organisation', ':organisaion'))
            ->setParameter('organisaion', $organisation);

        return $doctrineQb->getQuery()->getResult();
    }

    /**
     * Applies filters
     * @param QueryBuilder $qb
     * @param QueryInterface $query
     *
     * @return QueryBuilder
     */
    protected function applyListFilters(QueryBuilder $qb, QueryInterface $query)
    {
        if (method_exists($query, 'getEbsrSubmissionType') && $query->getEbsrSubmissionType()) {
            $qb->andWhere(
                $qb->expr()->eq($this->alias . '.ebsrSubmissionType', ':ebsrSubmissionType')
            )->setParameter('ebsrSubmissionType', $query->getEbsrSubmissionType());
        }

        if (method_exists($query, 'getEbsrSubmissionStatus') && $query->getEbsrSubmissionStatus()) {
            $qb->andWhere(
                $qb->expr()->eq($this->alias . '.ebsrSubmissionStatus', ':ebsrSubmissionStatus')
            )->setParameter('ebsrSubmissionStatus', $query->getEbsrSubmissionStatus());
        }
    }
}
