<?php

/**
 * Query Partial Interface
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryPartial;

use Doctrine\ORM\QueryBuilder;

/**
 * Query Partial Interface
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
interface QueryPartialInterface
{
    /**
     * Modify a query builder object with generic modifications
     *
     * @param QueryBuilder $qb
     * @param array $arguments
     */
    public function modifyQuery(QueryBuilder $qb, array $arguments = []);
}
