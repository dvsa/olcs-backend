<?php

namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Ebsr\EbsrSubmission as Entity;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Zend\Stdlib\ArraySerializableInterface as QryCmd;
use Dvsa\Olcs\Transfer\Query\OrderedQueryInterface;
use Dvsa\Olcs\Transfer\Query\PagedQueryInterface;
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
     * @param QueryBuilder   $qb
     * @param QueryInterface $query
     */
    protected function applyListFilters(QueryBuilder $qb, QueryInterface $query)
    {
        $qb->andWhere($qb->expr()->eq($this->alias . '.organisation', ':organisation'))
            ->setParameter('organisation', $query->getOrganisation());

        if (method_exists($query, 'getStatus') && !empty($query->getStatus())) {
            $qb->andWhere($qb->expr()->eq($this->alias . '.ebsrSubmissionStatus', ':ebsrSubmissionStatus'))
                ->setParameter('ebsrSubmissionStatus', $query->getStatus());
        }
        if (method_exists($query, 'getSubType') && !empty($query->getSubType())) {
            $qb->andWhere($qb->expr()->eq($this->alias . '.ebsrSubmissionType', ':ebsrSubmissionType'))
                ->setParameter('ebsrSubmissionType', $query->getSubType());
        }
    }
}
