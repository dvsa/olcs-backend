<?php

/**
 * Correspondence.php
 *
 * @author Joshua Curtis <josh.curtis@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr;
use Dvsa\Olcs\Api\Domain\Exception;
use Dvsa\Olcs\Api\Entity\Organisation\CorrespondenceInbox as Entity;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Doctrine\ORM\Query\Expr\Join;

/**
 * Class Correspondence
 *
 * @package Dvsa\Olcs\Api\Domain\Repository
 *
 * @author Joshua Curtis <josh.curtis@valtech.co.uk>
 */
class Correspondence extends AbstractRepository
{
    protected $entity = Entity::class;

    protected $alias = 'co';

    protected function applyListFilters(QueryBuilder $qb, QueryInterface $query)
    {
        $this->getQueryBuilder()->modifyQuery($qb)->with('licence', 'l');
        $this->getQueryBuilder()->modifyQuery($qb)->with('document');

        $qb->where($qb->expr()->eq('l.organisation', ':organisationId'));
        $qb->setParameter(':organisationId', $query->getOrganisation());
        $qb->orderBy($this->alias . '.createdOn', 'DESC');
    }

    protected function buildDefaultQuery(QueryBuilder $qb, $id)
    {
        return $this->getQueryBuilder()
            ->modifyQuery($qb)
            ->withRefdata()
            ->byId($id)
            ->with('document')
            ->with('licence');
    }

    public function getUnreadCountForOrganisation($organisationId)
    {
        $qb = $this->createQueryBuilder();

        $qb->join('co.licence', 'l', Join::WITH, $qb->expr()->eq('l.organisation', ':organisationId'));
        $qb->andWhere($qb->expr()->eq('co.accessed', ':accessed'));
        $qb->setParameter(':organisationId', $organisationId);
        $qb->setParameter(':accessed', 'N');

        $query = $qb->getQuery();

        $paginator = $this->getPaginator($query);
        return $paginator->count();
    }
}
