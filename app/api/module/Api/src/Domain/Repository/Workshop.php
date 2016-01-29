<?php

/**
 * Workshop
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Licence\Workshop as Entity;
use Doctrine\ORM\QueryBuilder;

/**
 * Workshop
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Workshop extends AbstractRepository
{
    protected $entity = Entity::class;

    /**
     * @NOTE This method can be overridden to extend the default resource bundle
     *
     * @param QueryBuilder $qb
     * @param int $id
     * @return \Dvsa\Olcs\Api\Domain\QueryBuilder
     */
    protected function buildDefaultQuery(QueryBuilder $qb, $id)
    {
        return parent::buildDefaultQuery($qb, $id)->withContactDetails();
    }
}
