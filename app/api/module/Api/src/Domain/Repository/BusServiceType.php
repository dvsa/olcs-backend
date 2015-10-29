<?php

/**
 * Bus Service Type
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Domain\Exception;
use Dvsa\Olcs\Api\Entity\Bus\BusServiceType as Entity;

/**
 * Bus Service Type
 */
class BusServiceType extends AbstractRepository
{
    protected $entity = Entity::class;

    public function fetchByTxcName($txcNames)
    {
        /* @var QueryBuilder $qb*/
        $qb = $this->createQueryBuilder();
        $this->getQueryBuilder()->modifyQuery($qb);
        $qb->andWhere($qb->expr()->in($this->alias . '.txcName', $txcNames));

        return $qb->getQuery()->execute();
    }
}
