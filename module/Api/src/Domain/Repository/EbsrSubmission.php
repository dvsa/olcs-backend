<?php

namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Ebsr\EbsrSubmission as Entity;
use Doctrine\ORM\Query;
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
        QueryInterface $query,
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

        if ($query instanceof PagedQueryInterface) {
            $this->getQueryBuilder()->paginate($query->getPage(), $query->getLimit());
        }

        if ($query instanceof OrderedQueryInterface) {
            if (!empty($query->getSort())) {
                // allow ordering by multiple columns
                $sortColumns = explode(',', $query->getSort());
                $orderColumns = explode(',', $query->getOrder());
                for ($i = 0; $i < count($sortColumns); $i++) {
                    // if multiple order value doesn't exist then use the first one
                    $order = isset($orderColumns[$i]) ? $orderColumns[$i] : $orderColumns[0];
                    $this->getQueryBuilder()->order($sortColumns[$i], $order);
                }
            }
        }

        if (!empty($query->getSubType())) {
            $qb->andWhere($qb->expr()->eq($this->alias . '.ebsrSubmissionType', ':ebsrSubmissionType'))
                ->setParameter('ebsrSubmissionType', $query->getSubType());
        }

        if (!empty($query->getStatus())) {
            $qb->andWhere($qb->expr()->eq('e.ebsrSubmissionStatus', ':ebsrSubmissionStatus'))
                ->setParameter('ebsrSubmissionStatus', $query->getStatus());
        }

        $qb->andWhere($qb->expr()->eq($this->alias . '.organisation', ':organisation'))
            ->setParameter('organisation', $organisation);

        return $qb->getQuery()->getResult($hydrateMode);
    }
}
