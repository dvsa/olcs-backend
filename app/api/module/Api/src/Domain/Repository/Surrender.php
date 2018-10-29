<?php

namespace Dvsa\Olcs\Api\Domain\Repository;

class Surrender extends AbstractRepository
{
    protected $entity = \Dvsa\Olcs\Api\Entity\Surrender::class;

    public function fetchStatus(int $id, $hydrateMode = Query::HYDRATE_OBJECT)
    {
        $qb = $this->createQueryBuilder();

        $this->getQueryBuilder()->modifyQuery($qb)
            ->withRefData()
            ->byId($id);

        $results = $qb->getQuery()->getResult($hydrateMode);

        if (empty($results)) {
            throw new NotFoundException('Resource not found');
        }

        return $results[0];
    }
}
