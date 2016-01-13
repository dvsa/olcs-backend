<?php

/**
 * With
 */
namespace Dvsa\Olcs\Api\Domain\QueryPartial;

use Doctrine\ORM\QueryBuilder;

/**
 * With
 */
final class With implements QueryPartialInterface
{
    private $i = 0;

    /**
     * Adds a left join on XX clause
     *
     * @param QueryBuilder $qb
     * @param array $arguments
     */
    public function modifyQuery(QueryBuilder $qb, array $arguments = [])
    {
        $property = $arguments[0];
        $alias = (isset($arguments[1]) ? $arguments[1] : 'w' . $this->i++);

        if (strpos($property, '.') === false) {
            $property = $qb->getRootAliases()[0] . '.' . $property;
        }

        $qb->leftJoin($property, $alias);
        $qb->addSelect($alias);
    }
}
