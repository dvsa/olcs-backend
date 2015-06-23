<?php

namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\View\BusRegSearchView as Entity;
use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;

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
}
