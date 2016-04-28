<?php

namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\View\BusRegSearchView as Entity;
use Dvsa\Olcs\Api\Entity\Bus\BusReg;
use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Transfer\Query\Bus\SearchViewList as ListQueryObject;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Domain\Exception;
use Doctrine\ORM\Query;

/**
 * BusRegSearchView
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class BusRegSearchView extends AbstractRepository
{
    protected $entity = Entity::class;

    /**
     * Fetch an entry from the view for a Reg No
     *
     * @param string $regNo
     *
     * @return Entity
     * @throws Exception\NotFoundException
     */
    public function fetchByRegNo($regNo)
    {
        $dqb = $this->createQueryBuilder();
        $this->getQueryBuilder()->modifyQuery($dqb);
        $dqb->where($dqb->expr()->eq($this->alias .'.regNo', ':regNo'))
            ->setParameter('regNo', $regNo);

        $results = $dqb->getQuery()->getResult();

        if (empty($results)) {
            throw new NotFoundException('Resource not found');
        }

        return $results[0];
    }

    /**
     *
     * @param QueryBuilder $qb
     * @param QueryInterface $query
     */
    protected function applyListFilters(QueryBuilder $qb, QueryInterface $query)
    {
        /** @var ListQueryObject $query */
        if (method_exists($query, 'getLicId') && !empty($query->getLicId())) {
            $qb->andWhere($qb->expr()->eq($this->alias . '.licId', ':licId'))
                ->setParameter('licId', $query->getLicId());
        }

        if (method_exists($query, 'getStatus') && !empty($query->getStatus())) {
            $qb->andWhere($qb->expr()->eq($this->alias . '.busRegStatus', ':status'))
                ->setParameter('status', $query->getStatus());
        }

        if (method_exists($query, 'getOrganisation') && !empty($query->getOrganisation())) {
            $qb->andWhere($qb->expr()->eq($this->alias . '.organisation', ':organisation'))
                ->setParameter('organisation', $query->getOrganisation());
        }
        echo $qb->getQuery()->getDQL();exit;
    }

    /**
     * Get Active Bus Regs
     *
     * @param int|\Dvsa\Olcs\Api\Entity\Licence\Licence $licence
     *
     * @return array
     */
    public function fetchActiveByLicence($licence)
    {
        $activeStatuses = [
            BusReg::STATUS_NEW,
            BusReg::STATUS_VAR,
            BusReg::STATUS_REGISTERED,
            BusReg::STATUS_CANCEL,
        ];

        $dqb = $this->createQueryBuilder();
        $this->getQueryBuilder()->modifyQuery($dqb);
        $dqb->where($dqb->expr()->eq($this->alias .'.licId', ':licence'))
            ->setParameter('licence', $licence);
        $dqb->andWhere($dqb->expr()->in($this->alias .'.busRegStatus', ':activeStatuses'))
            ->setParameter('activeStatuses', $activeStatuses);

        return $dqb->getQuery()->getResult();
    }

    /**
     * @param QueryInterface $query
     * @param int $hydrateMode
     *
     * @return \ArrayIterator|\Traversable
     */
    public function fetchDistinctList(QueryInterface $query, $hydrateMode = Query::HYDRATE_OBJECT)
    {
        $qb = $this->createQueryBuilder()
            ->addGroupBy($this->alias . '.' . $query->getContext());

        $result = $qb->getQuery()->getResult($hydrateMode);

        return $result;
    }
}
