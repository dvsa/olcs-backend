<?php

namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Ebsr\TxcInbox as Entity;

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
     * Fetch a list for a licence, filtered to include only not fulfilled and not draft
     *
     * @param int $licenceId
     *
     * @return array of Entity
     */
    public function fetchUnreadListForLocalAuthority(
        $localAuthority,
        $ebsrSubmissionType = null,
        $ebsrSubmissionStatus = null
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

        if (empty($localAuthority)) {
            $qb->where($qb->expr()->isNull($this->alias . '.localAuthority', ':localAuthority'));
        } else {
            $qb->where($qb->expr()->eq($this->alias . '.fileRead', '0'));
            $qb->andWhere($qb->expr()->eq($this->alias . '.localAuthority', ':localAuthority'))
                ->setParameter('localAuthority', $localAuthority);
        }

        if (!empty($ebsrSubmissionType)) {
            $qb->andWhere($qb->expr()->eq('e.ebsrSubmissionType', ':ebsrSubmissionType'))
                ->setParameter('ebsrSubmissionType', $ebsrSubmissionType);
        }

        if (!empty($ebsrSubmissionStatus)) {
            $qb->andWhere($qb->expr()->eq('e.ebsrSubmissionStatus', ':ebsrSubmissionStatus'))
                ->setParameter('ebsrSubmissionStatus', $ebsrSubmissionStatus);
        }

        return $qb->getQuery()->getResult();
    }
}
