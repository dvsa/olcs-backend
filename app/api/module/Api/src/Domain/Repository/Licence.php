<?php

/**
 * Licence
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Exception;

/**
 * Licence
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class Licence extends AbstractRepository
{
    protected $entity = '\Dvsa\Olcs\Api\Entity\Licence\Licence';

    public function fetchByCaseId($caseId, $hydrateMode = Query::HYDRATE_ARRAY, $version = null)
    {
        $qb = $this->createQueryBuilder();

        $this->getQueryBuilder()->modifyQuery($qb)->withRefdata()->with('trafficArea', 'ta');
        $qb->innerJoin($this->alias . '.cases', 'c');

        $qb->andWhere($qb->expr()->eq('c.id', ':caseId'));
        $qb->setParameter('caseId', $caseId);

        $results = $qb->getQuery()->getResult($hydrateMode);

        if (empty($results)) {
            throw new Exception\NotFoundException('Resource not found');
        }

        if ($hydrateMode === Query::HYDRATE_OBJECT && $version !== null) {
            $this->lock($results[0], $version);
        }

        return $results[0];
    }
}
