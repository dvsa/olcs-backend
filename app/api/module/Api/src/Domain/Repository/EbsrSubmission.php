<?php

namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Ebsr\EbsrSubmission as Entity;
use Doctrine\ORM\Query;
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
     * Fetch a list for an organisation, searchable on ebsrSubmissionType and ebsrSubmissionStatus
     *
     * @param $organisation
     * @param null $ebsrSubmissionType
     * @param null $ebsrSubmissionStatus
     * @param int $hydrateMode
     * @return array
     */
    public function fetchByOrganisation(
        $organisation,
        $ebsrSubmissionType = null,
        $ebsrSubmissionStatus = null,
        $hydrateMode = Query::HYDRATE_OBJECT
    ) {
        /* @var \Doctrine\Orm\QueryBuilder $qb*/
        $qb = $this->createQueryBuilder();

        $this->getQueryBuilder()->modifyQuery($qb)
            ->withRefdata()
            ->with($this->alias . '.busReg', 'b')
            ->with('b.licence', 'l')
            ->with('b.otherServices')
            ->with('l.organisation');

        if (!empty($ebsrSubmissionType)) {
            $qb->andWhere($qb->expr()->eq($this->alias . '.ebsrSubmissionType', ':ebsrSubmissionType'))
                ->setParameter('ebsrSubmissionType', $ebsrSubmissionType);
        }

        if (!empty($ebsrSubmissionStatus)) {
            $qb->andWhere($qb->expr()->eq('e.ebsrSubmissionStatus', ':ebsrSubmissionStatus'))
                ->setParameter('ebsrSubmissionStatus', $ebsrSubmissionStatus);
        }

        $qb->andWhere($qb->expr()->eq($this->alias . '.organisation', ':organisation'))
            ->setParameter('organisation', $organisation);

        return $qb->getQuery()->getResult($hydrateMode);
    }

    /**
     * Fetch a list of ebsr submission by organisation and status
     *
     * This is only used to bring back newly submitted documents and therefore doesn't bring back extra
     * information for efficiency reasons
     *
     * @param int $organisation
     * @param string $ebsrSubmissionStatus
     * @param int $hydrateMode
     * @return array
     */
    public function fetchForOrganisationByStatus(
        $organisation,
        $ebsrSubmissionStatus,
        $hydrateMode = Query::HYDRATE_OBJECT
    ) {
        /* @var \Doctrine\Orm\QueryBuilder $qb*/
        $qb = $this->createQueryBuilder();

        $this->getQueryBuilder()->modifyQuery($qb);

        $qb->andWhere($qb->expr()->eq($this->alias . '.ebsrSubmissionStatus', ':ebsrSubmissionStatus'))
            ->setParameter('ebsrSubmissionStatus', $ebsrSubmissionStatus);

        $qb->andWhere($qb->expr()->eq($this->alias . '.organisation', ':organisation'))
            ->setParameter('organisation', $organisation);

        return $qb->getQuery()->getResult($hydrateMode);
    }

    /**
     * @param QueryBuilder $qb
     * @param QueryInterface $query
     * @param array $compositeFields
     */
    protected function buildDefaultListQuery(QueryBuilder $qb, QueryInterface $query, $compositeFields = [])
    {
        parent::buildDefaultListQuery($qb, $query, $compositeFields);

        // join in person details
        $this->getQueryBuilder()->with($this->alias . '.busReg', 'b')
            ->with('b.licence', 'l')
            ->with('b.otherServices')
            ->with('l.organisation');
    }

    /**
     * Applies filters to list queries. Note we always ignore newly uploaded files until they've been fully submitted
     *
     * @param QueryBuilder $qb
     * @param QueryInterface $query
     */
    protected function applyListFilters(QueryBuilder $qb, QueryInterface $query)
    {
        $qb->andWhere($qb->expr()->eq($this->alias . '.organisation', ':organisation'))
            ->setParameter('organisation', $query->getOrganisation());

        if (method_exists($query, 'getStatus') && !empty($query->getStatus())) {
            $qb->andWhere($qb->expr()->in($this->alias . '.ebsrSubmissionStatus', ':ebsrSubmissionStatus'))
                ->setParameter('ebsrSubmissionStatus', $query->getStatus());
        }
        if (method_exists($query, 'getSubType') && !empty($query->getSubType())) {
            $qb->andWhere($qb->expr()->eq($this->alias . '.ebsrSubmissionType', ':ebsrSubmissionType'))
                ->setParameter('ebsrSubmissionType', $query->getSubType());
        }

        //always exclude entities that are in the process of being submitted
        $qb->andWhere($qb->expr()->neq($this->alias . '.ebsrSubmissionStatus', ':ebsrtSubmissionStatus'))
            ->setParameter('ebsrtSubmissionStatus', Entity::UPLOADED_STATUS);
    }
}
