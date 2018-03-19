<?php

/**
 * EcmtPermits
 *
 * @author Tonci Vidovic <tonci.vidovic@capgemini.com>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Entity\EcmtPermits as Entity;
use Dvsa\Olcs\Api\Entity\EcmtPermitsApplication as PermitsApplicationEntity;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Doctrine\ORM\Query\Expr\Join;

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
     * @return void
     */
    public function fetchData($query)
    {

        $hydrateMode = Query::HYDRATE_OBJECT;
        $qb = $this->createQueryBuilder();
        $qb->andWhere($qb->expr()->eq($this->alias . '.sectorId', ':bySector'))->setParameter('bySector', $query->getSectorId());
        $qb->orderBy($this->alias .'.'. $query->getSort(),$query->getOrder());

        $results = $qb->getQuery()->getResult($hydrateMode);

        $data = [];

        foreach ($results as $row)
        {
            $r = $row->getEcmtPermitsApplication()->getLicence()->getLicNo();
            $rr = $row->getEcmtPermitsApplication()->getLicence()->getOrganisation()->getName();
            $row->setEcmtPermitsApplication($r);
            $row->setStartDate($rr);
            $data[] = $row;
        }

        return new \ArrayIterator($data);


    }
}
