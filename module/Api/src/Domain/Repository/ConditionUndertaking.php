<?php

/**
 * ConditionUndertaking
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Exception;
use Dvsa\Olcs\Api\Domain\QueryBuilderInterface;
use Zend\Stdlib\ArraySerializableInterface as QryCmd;
use Dvsa\Olcs\Api\Entity\Cases\ConditionUndertaking as Entity;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * ConditionUndertaking
 */
class ConditionUndertaking extends AbstractRepository
{
    protected $entity = Entity::class;

    public function __construct(
        EntityManagerInterface $em,
        QueryBuilderInterface $queryBuilder
    ) {
        parent::__construct($em, $queryBuilder);
    }

    /**
     * Fetch the default record by it's id
     *
     * @param Query|QryCmd $query
     * @param int $hydrateMode

     * @return mixed
     * @throws Exception\NotFoundException
     * @throws Exception\VersionConflictException
     */
    public function fetchUsingCaseId(QryCmd $query, $hydrateMode = Query::HYDRATE_OBJECT)
    {
        /* @var \Doctrine\Orm\QueryBuilder $qb*/
        $qb = $this->createQueryBuilder();

        $this->applyFetchJoins($qb);

        $this->getQueryBuilder()->modifyQuery($qb)
            ->byId($query->getId());

        $qb->andWhere($qb->expr()->eq($this->alias . '.case', ':byCase'))
            ->setParameter('byCase', $query->getCase());

        $result = $qb->getQuery()->getResult($hydrateMode);

        if (empty($result)) {
            throw new Exception\NotFoundException('Resource not found');
        }

        return $result[0];
    }

    /**
     * Apply List Filters
     * @param QueryBuilder $qb
     * @param QueryInterface $query
     */
    protected function applyListFilters(QueryBuilder $qb, QueryInterface $query)
    {
        $qb->andWhere($qb->expr()->eq($this->alias . '.case', ':byCase'))
            ->setParameter('byCase', $query->getCase());
    }


    /**
     * Add List Joins
     * @param QueryBuilder $qb
     */
    protected function applyListJoins(QueryBuilder $qb)
    {
        $this->getQueryBuilder()->modifyQuery($qb)
            ->withRefdata()
            ->with('operatingCentre', 'oc')
            ->with('oc.address')
            ->with('createdBy')
            ->with('lastModifiedBy');
    }

    /**
     * Add Fetch Joins
     * @param QueryBuilder $qb
     */
    protected function applyFetchJoins(QueryBuilder $qb)
    {
        $this->getQueryBuilder()->modifyQuery($qb)
            ->withRefdata()
            ->with('case')
            ->with('operatingCentre', 'oc')
            ->with('oc.address')
            ->with('createdBy')
            ->with('lastModifiedBy');

    }
}
