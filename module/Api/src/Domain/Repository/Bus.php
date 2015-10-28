<?php

/**
 * Bus
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Bus\BusReg as Entity;
use Dvsa\Olcs\Api\Domain\Exception;
use Zend\Stdlib\ArraySerializableInterface as QryCmd;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

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
            ->with('otherServices')
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

    public function fetchLatestUsingRegNo($regNo, $hydrateMode = Query::HYDRATE_OBJECT)
    {
        $qb = $this->createQueryBuilder();

        $qb->andWhere($qb->expr()->eq($this->alias . '.regNo', ':byRegNo'))
            ->setParameter('byRegNo', $regNo);
        $qb->addOrderBy($this->alias . '.id', 'DESC');

        $results = $qb->getQuery()->getResult($hydrateMode);

        if (!empty($results)) {
            return $results[0];
        }

        throw new Exception\NotFoundException('Resource not found');
    }

    /**
     * Applies filters
     * @param QueryBuilder $qb
     * @param QueryInterface $query
     */
    protected function applyListFilters(QueryBuilder $qb, QueryInterface $query)
    {
        $qb->andWhere($qb->expr()->eq($this->alias . '.routeNo', ':byRouteNo'))
            ->setParameter('byRouteNo', $query->getRouteNo());

        if (method_exists($query, 'getVariationNo')) {
            $qb->andWhere($qb->expr()->lt($this->alias . '.variationNo', ':byVariationNo'))
                ->setParameter('byVariationNo', $query->getVariationNo());
        }

        if (method_exists($query, 'getLicenceId')) {
            $qb->andWhere($qb->expr()->eq($this->alias . '.licence', ':byLicence'))
                ->setParameter('byLicence', $query->getLicenceId());
        }
    }

    /**
     * Applies list joins
     * @param QueryBuilder $qb
     */
    protected function applyListJoins(QueryBuilder $qb)
    {
        $this->getQueryBuilder()->modifyQuery($qb)
            ->with('busNoticePeriod')
            ->with('status');
    }
}
