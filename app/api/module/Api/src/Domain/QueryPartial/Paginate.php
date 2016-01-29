<?php

/**
 * Paginate
 */
namespace Dvsa\Olcs\Api\Domain\QueryPartial;

use Doctrine\ORM\QueryBuilder;

/**
 * Paginate
 */
final class Paginate implements QueryPartialInterface
{
    /**
     * Adds a where id = XX clause
     *
     * @param QueryBuilder $qb
     * @param array $arguments
     */
    public function modifyQuery(QueryBuilder $qb, array $arguments = [])
    {
        list($page, $limit) = $arguments;
        $qb->setFirstResult(($page - 1) * $limit);
        $qb->setMaxResults($limit);
    }
}
