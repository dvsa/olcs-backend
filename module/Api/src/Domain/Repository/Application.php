<?php

/**
 * Application
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Zend\Stdlib\ArraySerializableInterface;

/**
 * Application
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class Application extends AbstractRepository
{
    protected $entity = '\Olcs\Db\Entity\Application';

    /**
     * Fetch the default application record by it's id
     *
     * @param ArraySerializableInterface $query
     * @return array
     */
    public function fetchUsingId(ArraySerializableInterface $query)
    {
        $qb = $this->createQueryBuilder();

        $this->getQueryBuilder()->modifyQuery($qb)->withRefdata()->byId($query->getId());

        $results = $qb->getQuery()->getArrayResult();

        return empty($results) ? null : $results[0];
    }
}
