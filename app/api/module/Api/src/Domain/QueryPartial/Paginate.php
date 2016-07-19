<?php

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
     * @param QueryBuilder $qb        Query Builder
     * @param array        $arguments Page and Limit parameters
     *
     * @return void
     */
    public function modifyQuery(QueryBuilder $qb, array $arguments = [])
    {
        list($page, $limit) = array_map('intval', $arguments);

        $qb->setFirstResult(max($page -1, 0) * $limit);
        $qb->setMaxResults((int) $limit);
    }
}
