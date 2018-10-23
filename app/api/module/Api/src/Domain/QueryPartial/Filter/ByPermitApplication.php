<?php
/**
 * Filter By Permit Application
 *
 * @author Andy Newton <andy@vitri.ltd>
 */

namespace Dvsa\Olcs\Api\Domain\QueryPartial\Filter;

use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Domain\QueryPartial\QueryPartialInterface;

final class ByPermitApplication implements QueryPartialInterface
{
    /**
     * Adds a where permit application = XX clause only if id is specified
     *
     * @param QueryBuilder $qb
     * @param array $arguments
     */
    public function modifyQuery(QueryBuilder $qb, array $arguments = [])
    {
        list($ecmtPermitApplicationId) = $arguments;

        if ($ecmtPermitApplicationId !== null) {
            list($alias) = $qb->getRootAliases();

            $qb->andWhere($qb->expr()->eq($alias . '.ecmtPermitApplication', ':ecmtPermitApplicationId'))
                ->setParameter('ecmtPermitApplicationId', $ecmtPermitApplicationId);
        }
    }
}
