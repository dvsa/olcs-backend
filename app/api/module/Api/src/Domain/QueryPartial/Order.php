<?php

/**
 * Paginate
 */
namespace Dvsa\Olcs\Api\Domain\QueryPartial;

use Doctrine\ORM\QueryBuilder;

/**
 * Paginate
 */
final class Order implements QueryPartialInterface
{
    /**
     * Adds a where id = XX clause
     *
     * @param QueryBuilder $qb
     * @param array $arguments
     */
    public function modifyQuery(QueryBuilder $qb, array $arguments = [])
    {
        list($sort, $order) = $arguments;

        list($alias) = $qb->getRootAliases();

        $qb->orderBy($alias . '.' . $sort, $order);
    }
}
