<?php

/**
 * Application
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Zend\Stdlib\ArraySerializableInterface;
use Doctrine\ORM\Query;

/**
 * Application
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class Application extends AbstractRepository
{
    protected $entity = '\Dvsa\Olcs\Api\Entity\Application\Application';

    /**
     * Fetch the default application record by it's id
     *
     * @param ArraySerializableInterface $query
     * @return array
     */
    public function fetchUsingId(ArraySerializableInterface $query, $hydrateMode = Query::HYDRATE_ARRAY)
    {
        $qb = $this->createQueryBuilder();

        $this->getQueryBuilder()->modifyQuery($qb)->withRefdata()->byId($query->getId());

        $results = $qb->getQuery()->getResult($hydrateMode);

        return empty($results) ? null : $results[0];
    }
}
