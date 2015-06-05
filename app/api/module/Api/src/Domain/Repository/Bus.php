<?php

/**
 * Bus
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Bus\BusReg as Entity;
use Dvsa\Olcs\Api\Domain\Exception;
use Zend\Stdlib\ArraySerializableInterface as QryCmd;
use Doctrine\ORM\Query;

/**
 * Bus
 */
class Bus extends AbstractRepository
{
    protected $entity = Entity::class;

    /**
     * Fetch the default record by it's id
     *
     * @param Query|QryCmd $query
     * @param int $hydrateMode
     * @param null $version
     * @return mixed
     * @throws Exception\NotFoundException
     * @throws Exception\VersionConflictException
     */
    public function fetchUsingId(QryCmd $query, $hydrateMode = Query::HYDRATE_OBJECT, $version = null)
    {
        /* @var \Doctrine\Orm\QueryBuilder $qb*/
        $qb = $this->createQueryBuilder();

        $this->getQueryBuilder()->modifyQuery($qb)
            ->withRefdata()
            ->with('busNoticePeriod')
            ->with('busServiceTypes')
            ->with('trafficAreas')
            ->with('localAuthoritys')
            ->with('subsidised')
            ->byId($query->getId());

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
