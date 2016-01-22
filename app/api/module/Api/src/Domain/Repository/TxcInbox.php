<?php

namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Ebsr\TxcInbox as Entity;
use Doctrine\ORM\Query;
use Zend\Stdlib\ArraySerializableInterface as QryCmd;
use Dvsa\Olcs\Transfer\Query\OrderedQueryInterface;
use Dvsa\Olcs\Transfer\Query\PagedQueryInterface;

/**
 * TxcInbox
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class TxcInbox extends AbstractRepository
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

        $doctrineQb->andWhere($doctrineQb->expr()->eq($this->alias . '.organisation', ':organisation'))
            ->setParameter('organisation', $organisation);

        return $doctrineQb->getQuery()->getResult();
    }

    /**
     * Fetch a list of unread docs filtered by local authority, submission type and status for a given bus reg id
     *
     * @param $busReg
     * @param $organisationId
     * @param int $hydrateMode
     * @return array
     */
    public function fetchListForOrganisationByBusReg($busReg, $organisationId, $hydrateMode = Query::HYDRATE_OBJECT)
    {
        /* @var \Doctrine\Orm\QueryBuilder $qb*/
        $qb = $this->createQueryBuilder();

        $this->getQueryBuilder()->modifyQuery($qb)
            ->withRefdata()
            ->with('busReg', 'b');

        $qb->where($qb->expr()->eq('b.id', ':busReg'))
            ->setParameter('busReg', $busReg);

        $qb->andWhere($qb->expr()->isNull($this->alias . '.localAuthority'));
        $qb->andWhere($qb->expr()->eq($this->alias . '.organisation', ':organisation'))
            ->setParameter('organisation', $organisationId);

        return $qb->getQuery()->getResult($hydrateMode);
    }

    /**
     * Fetch a list of unread docs filtered by local authority, submission type and status for a given bus reg id
     *
     * @param int $busReg
     * @param int $localAuthorityId
     * @param int $hydrateMode
     * @return array
     */
    public function fetchListForLocalAuthorityByBusReg($busReg, $localAuthorityId, $hydrateMode = Query::HYDRATE_OBJECT)
    {
        /* @var \Doctrine\Orm\QueryBuilder $qb*/
        $qb = $this->createQueryBuilder();

        $this->getQueryBuilder()->modifyQuery($qb)
            ->withRefdata()
            ->with('busReg', 'b');

        $qb->where($qb->expr()->eq('b.id', ':busReg'))
            ->setParameter('busReg', $busReg);

        if (empty($localAuthorityId)) {
            $qb->andWhere($qb->expr()->isNull($this->alias . '.localAuthority'));
        } else {
            $qb->andWhere($qb->expr()->eq($this->alias . '.fileRead', '0'));
            $qb->andWhere($qb->expr()->eq($this->alias . '.localAuthority', ':localAuthority'))
                ->setParameter('localAuthority', $localAuthorityId);
        }

        return $qb->getQuery()->getResult($hydrateMode);
    }

    /**
     * Fetch a list of unread docs filtered by local authority, submission type and status
     *
     * @param $localAuthority
     * @param null $ebsrSubmissionType
     * @param null $ebsrSubmissionStatus
     * @param int $hydrateMode
     * @return array
     */
    public function fetchUnreadListForLocalAuthority(
        QryCmd $query,
        $localAuthority,
        $hydrateMode = Query::HYDRATE_OBJECT
    ) {
        $qb = $this->getUnreadListQuery($query);

        if (empty($localAuthority)) {
            $qb->andWhere($qb->expr()->isNull($this->alias . '.localAuthority'));
        } else {
            $qb->andWhere($qb->expr()->eq($this->alias . '.fileRead', '0'));
            $qb->andWhere($qb->expr()->eq($this->alias . '.localAuthority', ':localAuthority'))
                ->setParameter('localAuthority', $localAuthority);
        }

        return $qb->getQuery()->getResult($hydrateMode);
    }

    /**
     * General Query for unread txc inbox list. Used by LAs
     *
     * @param null $ebsrSubmissionType
     * @param null $ebsrSubmissionStatus
     * @return \Doctrine\Orm\QueryBuilder
     */
    private function getUnreadListQuery(QryCmd $query)
    {
        /* @var \Doctrine\Orm\QueryBuilder $qb*/
        $qb = $this->createQueryBuilder();

        $this->getQueryBuilder()->modifyQuery($qb)
            ->withRefdata()
            ->with('m.busReg', 'b')
            ->with('b.ebsrSubmissions', 'e')
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

        if (!empty($query->getEbsrSubmissionType())) {
            $qb->andWhere($qb->expr()->eq('e.ebsrSubmissionType', ':ebsrSubmissionType'))
                ->setParameter('ebsrSubmissionType', $query->getEbsrSubmissionType());
        }

        if (!empty($query->getEbsrSubmissionStatus())) {
            $qb->andWhere($qb->expr()->eq('e.ebsrSubmissionStatus', ':ebsrSubmissionStatus'))
                ->setParameter('ebsrSubmissionStatus', $query->getEbsrSubmissionStatus());
        }

        return $qb;
    }
}
