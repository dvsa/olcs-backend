<?php

namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\View\BusRegSearchView as Entity;
use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Transfer\Query\Bus\SearchViewList as ListQueryObject;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Domain\Exception;

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

        if (!empty($query->getLicId())) {

            $qb->andWhere($qb->expr()->eq($this->alias . '.licId', ':licence'))
                ->setParameter('licence', $query->getLicId());
        }

        if (!empty($query->getStatus())) {

            $qb->andWhere($qb->expr()->eq($this->alias . '.busRegStatus', ':status'))
                ->setParameter('status', $query->getStatus());
        }

    }
}
