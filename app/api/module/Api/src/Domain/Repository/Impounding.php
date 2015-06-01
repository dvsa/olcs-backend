<?php

/**
 * Impounding
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Exception;
use Dvsa\Olcs\Api\Domain\QueryBuilderInterface;
use Zend\Stdlib\ArraySerializableInterface as QryCmd;
use Dvsa\Olcs\Api\Entity\Cases\Impounding as Entity;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Impounding
 */
class Impounding extends AbstractRepository
{
    protected $entity = Entity::class;

    public function __construct(
        EntityManagerInterface $em,
        QueryBuilderInterface $queryBuilder
    ) {
        parent::__construct($em, $queryBuilder);
    }

    /**
     *
     * @param QueryBuilder $qb
     * @param QueryInterface $query
     */
    protected function applyListFilters(QueryBuilder $qb, QueryInterface $query)
    {
        $this->getQueryBuilder()->modifyQuery($qb)
            ->with('presidingTc');

        $qb->andWhere($qb->expr()->eq($this->alias . '.case', ':byCase'))
            ->setParameter('byCase', $query->getCase());
    }
}
