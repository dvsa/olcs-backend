<?php

namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\Query\Expr\Join;
use Dvsa\Olcs\Api\Entity\Organisation\CorrespondenceInbox as Entity;
use Dvsa\Olcs\Transfer\Query\Correspondence\Correspondences;

/**
 * Class Correspondence
 *
 * @author Joshua Curtis <josh.curtis@valtech.co.uk>
 */
class Correspondence extends AbstractRepository
{
    protected $entity = Entity::class;

    protected $alias = 'co';

    /**
     * Fetch List of documents
     *
     * @param Correspondences $query Query
     *
     * @return \Doctrine\ORM\Internal\Hydration\IterableResult
     */
    public function fetchDocumentsList(Correspondences $query)
    {
        $qb = $this->createQueryBuilder();

        $qb
            ->select(
                $this->alias . '.id',
                $this->alias . '.accessed',
                $this->alias . '.createdOn',
                'l.id as licId, l.licNo',
                'IDENTITY(l.status) as licStatus',
                'd.description as docDesc'
            )
            ->join($this->alias . '.licence', 'l')
            ->join($this->alias . '.document', 'd')
            ->where(
                $qb->expr()->eq('l.organisation', ':ORG_ID')
            )
            ->setParameter('ORG_ID', $query->getOrganisation())
            ->orderBy($this->alias . '.createdOn', 'DESC');

        return $qb->getQuery()->iterate();
    }

    /**
     * Get Unread Count
     *
     * @param int $organisationId Org Id
     *
     * @return int
     */
    public function getUnreadCountForOrganisation($organisationId)
    {
        $qb = $this->createQueryBuilder();

        $qb->select('COUNT(co)');
        $qb->join('co.licence', 'l', Join::WITH, $qb->expr()->eq('l.organisation', ':organisationId'));
        $qb->andWhere($qb->expr()->eq('co.accessed', ':accessed'));
        $qb->setParameter(':organisationId', $organisationId);
        $qb->setParameter(':accessed', 'N');

        return $qb->getQuery()->getSingleScalarResult();
    }
}
