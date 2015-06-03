<?php

/**
 * Trailers
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Exception;
use Dvsa\Olcs\Api\Entity\Licence\Trailer as Entity;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Trailers
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
class Trailer extends AbstractRepository
{
    protected $entity = Entity::class;

    public function fetchByLicenceId(QueryInterface $query, $hydrateMode = Query::HYDRATE_ARRAY, $version = null)
    {
        unset($version);

        $qb = $this->createQueryBuilder();

        $qb->where($qb->expr()->eq($this->alias . '.licence', ':licenceId'));
        $qb->setParameter(':licenceId', $query->getLicence());

        $results = $qb->getQuery()->getResult($hydrateMode);

        return $results;
    }

    protected function applyListFilters(QueryBuilder $qb, QueryInterface $query)
    {
        $qb->where($qb->expr()->eq($this->alias . '.licence', ':licenceId'));
        $qb->setParameter(':licenceId', $query->getLicence());
    }
}
