<?php

/**
 * BusRegHistory view repo
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\View\BusRegHistoryView as Entity;
use Dvsa\Olcs\Api\Domain\Exception;
use Zend\Stdlib\ArraySerializableInterface as QryCmd;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Transfer\Query\Bus\HistoryList as DTO;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Doctrine\ORM\QueryBuilder;

/**
 * BusRegHistory view repo
 */
class BusRegHistory extends AbstractRepository
{
    protected $entity = Entity::class;

    /**
     * @param QueryBuilder $qb
     * @param DTO $query
     */
    protected function applyListFilters(QueryBuilder $qb, QueryInterface $query)
    {
        /** DTO $query */
        $qb->andWhere($this->alias . '.busReg = :busReg');
        $qb->setParameter('busReg', $query->getBusReg());
    }

    public function save($entity)
    {
        throw \Exception('You cannot save to a view');
    }

    public function delete($entity)
    {
        throw \Exception('You delete the contents of a view');
    }
}
