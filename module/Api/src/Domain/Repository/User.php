<?php

/**
 * User
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\User\User as Entity;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * User
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class User extends AbstractRepository
{
    protected $entity = Entity::class;
    protected $alias = 'u';

    /**
     * @param QueryBuilder $qb
     * @param QueryInterface $query
     */
    protected function buildDefaultListQuery(QueryBuilder $qb, QueryInterface $query)
    {
        parent::buildDefaultListQuery($qb, $query);

        // join in person details
        $this->getQueryBuilder()->with('contactDetails', 'cd')->with('cd.person');
    }

    /**
     * @param QueryBuilder $qb
     * @param QueryInterface $query
     */
    protected function applyListFilters(QueryBuilder $qb, QueryInterface $query)
    {
        // filter by organisation if it has been specified
        if ($query->getOrganisation()) {
            $qb->join('u.organisationUsers', 'ou', \Doctrine\ORM\Query\Expr\Join::WITH, 'ou.organisation = :organisation');
            $qb->setParameter('organisation', $query->getOrganisation());
        }
    }
}
