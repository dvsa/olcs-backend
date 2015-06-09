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

    protected function applyListFilters(QueryBuilder $qb, QueryInterface $query)
    {
        $qb->leftJoin(
            'm.licence',
            'l',
            Expr\Join::LEFT_JOIN
        );

        $qb->where($qb->expr()->eq('l.organisation', ':organisationId'));
        $qb->setParameter(':organisationId', $query->getOrganisation());

        $this->getQueryBuilder()->modifyQuery($qb)->with('document');
        $this->getQueryBuilder()->modifyQuery($qb)->with('licence');
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
}
