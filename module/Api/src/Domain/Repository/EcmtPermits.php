<?php

/**
 * EcmtPermits
 *
 * @author Tonci Vidovic <tonci.vidovic@capgemini.com>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Entity\EcmtPermits as Entity;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Sectors
 *
 * @author Tonci Vidovic <tonci.vidovic@capgemini.com>
 */
class EcmtPermits extends AbstractRepository
{

    protected $entity = Entity::class;

    /**
     * Applies filters
     *
     * @param QueryBuilder   $qb    doctrine query builder
     * @param QueryInterface $query query being run
     *
     * @return array
     */
    public function fetchData($query)
    {

        $hydrateMode = Query::HYDRATE_OBJECT;

        $qb = $this->createQueryBuilder();

        $this->getQueryBuilder()->modifyQuery($qb)
          ->withRefdata()
          ->order($this->alias .'.'. $query->getSort(),$query->getOrder())
          ->paginate($query->getPage(), $query->getLimit());

        $qb->andWhere($qb->expr()->eq($this->alias . '.sectorId', ':bySector'))->setParameter('bySector', $query->getSectorId());

        $results = $this->fetchPaginatedObj($qb, $hydrateMode);

        $data = [];

        foreach ($results as $row)
        {
            $r = $row->getEcmtPermitsApplication()->getLicence()->getLicNo();
            $rr = $row->getEcmtPermitsApplication()->getLicence()->getOrganisation()->getName();
            $row->setEcmtPermitsApplication($r);
            $row->setStartDate($rr);
            $data[] = $row;
        }

        return [
          'result' => new \ArrayIterator($data),
          'count' => $this->fetchPaginatedCount($qb)
        ];

    }

    /**
     * Abstracted paginator logic so it can be re-used with alternative queries
     *
     * @param QueryBuilder $qb          Doctrine query builder
     * @param int          $hydrateMode Hydrate mode
     *
     * @return object
     */
    public function fetchPaginatedObj(QueryBuilder $qb, $hydrateMode = Query::HYDRATE_ARRAY)
    {
        $query = $qb->getQuery();
        $query->setHydrationMode($hydrateMode);

        if ($this->query instanceof PagedQueryInterface) {
            $paginator = $this->getPaginator($query);

            return $paginator->getIterator($hydrateMode);
        }

        return $query->getResult($hydrateMode);
    }
}
