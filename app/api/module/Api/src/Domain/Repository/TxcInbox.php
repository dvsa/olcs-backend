<?php

namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Ebsr\TxcInbox as Entity;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Zend\Stdlib\ArraySerializableInterface as QryCmd;
use Dvsa\Olcs\Transfer\Query\OrderedQueryInterface;
use Dvsa\Olcs\Transfer\Query\PagedQueryInterface;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

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
        QueryInterface $query,
        $hydrateMode = Query::HYDRATE_OBJECT
    ) {
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

        $qb->andWhere($qb->expr()->eq($this->alias . '.fileRead', '0'));
        $qb->andWhere($qb->expr()->eq($this->alias . '.localAuthority', ':localAuthority'))
            ->setParameter('localAuthority', $query->getLocalAuthority());

        if (!empty($query->getSubType())) {
            $qb->andWhere($qb->expr()->eq('e.ebsrSubmissionType', ':ebsrSubmissionType'))
                ->setParameter('ebsrSubmissionType', $this->getRefdataReference($query->getSubType()));
        }
        if (!empty($query->getStatus())) {
            $qb->andWhere($qb->expr()->eq('e.ebsrSubmissionStatus', ':ebsrSubmissionStatus'))
                ->setParameter('ebsrSubmissionStatus', $this->getRefdataReference($query->getStatus()));
        }

        return $this->fetchList($query, $hydrateMode);
    }
}
