<?php

/**
 * IrhpApplicationView
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\View\IrhpApplicationView as Entity;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use \Doctrine\ORM\QueryBuilder;

class IrhpApplicationView extends AbstractRepository
{
    protected $entity = Entity::class;
    protected $alias = 'iav';

    /**
     * Apply list filters
     *
     * @param QueryBuilder   $qb    Query Builder
     * @param QueryInterface $query Query
     *
     * @return void
     */
    protected function applyListFilters(QueryBuilder $qb, QueryInterface $query)
    {
        if (method_exists($query, 'getLicence') && $query->getLicence() !== null) {
            $qb->andWhere($qb->expr()->eq($this->alias . '.licenceId', $query->getLicence()));
        }

        if (method_exists($query, 'getOrganisation') && $query->getOrganisation() !== null) {
            $qb->andWhere($qb->expr()->eq($this->alias . '.organisationId', $query->getOrganisation()));
        }

        if (method_exists($query, 'getIrhpApplicationStatuses') && !empty($query->getIrhpApplicationStatuses())) {
            $qb->andWhere($qb->expr()->in($this->alias . '.statusId', $query->getIrhpApplicationStatuses()));
        }
    }
}
