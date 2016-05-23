<?php

namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Entity\Organisation\CorrespondenceInbox as Entity;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

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
     * @param QueryBuilder   $qb
     * @param \Dvsa\Olcs\Transfer\Query\Correspondence\Correspondences $query
     */
    protected function applyListFilters(QueryBuilder $qb, QueryInterface $query)
    {
        $this->getQueryBuilder()->modifyQuery($qb)->with('licence', 'l');
        $this->getQueryBuilder()->modifyQuery($qb)->with('document');

        $qb->where($qb->expr()->eq('l.organisation', ':organisationId'));
        $qb->setParameter(':organisationId', $query->getOrganisation());
        $qb->orderBy($this->alias . '.createdOn', 'DESC');
    }

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
