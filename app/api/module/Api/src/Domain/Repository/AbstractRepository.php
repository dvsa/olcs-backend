<?php

/**
 * Abstract Repository
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Domain\QueryBuilderInterface;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Api\Entity\System\SubCategory;
use Dvsa\Olcs\Api\Entity\System\RefData as RefDataEntity;
use Dvsa\Olcs\Api\Domain\Exception;
use Doctrine\ORM\Query;
use Zend\Stdlib\ArraySerializableInterface as QryCmd;
use Doctrine\ORM\OptimisticLockException;
use Dvsa\Olcs\Transfer\Query\OrderedQueryInterface;
use Dvsa\Olcs\Transfer\Query\PagedQueryInterface;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * Abstract Repository
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractRepository implements RepositoryInterface
{
    protected $entity = 'Define\Me';

    protected $alias = 'm';

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var \Dvsa\Olcs\Api\Domain\QueryBuilder
     */
    private $queryBuilder;

    public function __construct(EntityManagerInterface $em, QueryBuilderInterface $queryBuilder)
    {
        $this->em = $em;
        $this->queryBuilder = $queryBuilder;
    }

    /**
     * Fetch the default record by it's id
     *
     * @param Query|QryCmd $query
     * @param int $hydrateMode
     * @param null $version
     * @return mixed
     * @throws Exception\NotFoundException
     * @throws Exception\VersionConflictException
     */
    public function fetchUsingId(QryCmd $query, $hydrateMode = Query::HYDRATE_OBJECT, $version = null)
    {
        return $this->fetchById($query->getId(), $hydrateMode, $version);
    }

    public function fetchById($id, $hydrateMode = Query::HYDRATE_OBJECT, $version = null)
    {
        $qb = $this->createQueryBuilder();

        $this->buildDefaultQuery($qb, $id);

        $results = $qb->getQuery()->getResult($hydrateMode);

        if (empty($results)) {
            throw new Exception\NotFoundException('Resource not found');
        }

        if ($hydrateMode === Query::HYDRATE_OBJECT && $version !== null) {
            $this->lock($results[0], $version);
        }

        return $results[0];
    }

    /**
     * @param QueryInterface $query
     * @param int $hydrateMode
     * @return array
     */
    public function fetchList(QueryInterface $query, $hydrateMode = Query::HYDRATE_ARRAY)
    {
        $qb = $this->createQueryBuilder();

        $this->buildDefaultListQuery($qb, $query);
        $this->applyListFilters($qb, $query);

        $query = $qb->getQuery();
        $query->setHydrationMode($hydrateMode);

        $paginator = new Paginator($query);
        return $paginator->getIterator($hydrateMode);
    }

    /**
     * @param QueryInterface $query
     * @param int $hydrateMode
     * @return int
     */
    public function fetchCount(QueryInterface $query)
    {
        $qb = $this->createQueryBuilder();

        $this->buildDefaultListQuery($qb, $query);
        $this->applyListFilters($qb, $query);

        $query = $qb->getQuery();

        $paginator = new Paginator($query);
        return $paginator->count();
    }

    protected function applyListFilters(QueryBuilder $qb, QueryInterface $query)
    {

    }

    public function lock($entity, $version)
    {
        if (!($entity instanceof $this->entity)) {
            throw new Exception\RuntimeException('This repository can only lock entities of type ' . $this->entity);
        }

        try {
            $this->getEntityManager()->lock($entity, LockMode::OPTIMISTIC, $version);
        } catch (OptimisticLockException $ex) {
            throw new Exception\VersionConflictException();
        }
    }

    public function save($entity)
    {
        if (!($entity instanceof $this->entity)) {
            throw new Exception\RuntimeException('This repository can only save entities of type ' . $this->entity);
        }

        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
    }

    public function delete($entity)
    {
        if (!($entity instanceof $this->entity)) {
            throw new Exception\RuntimeException('This repository can only delete entities of type ' . $this->entity);
        }

        $this->getEntityManager()->remove($entity);
        $this->getEntityManager()->flush();
    }

    public function beginTransaction()
    {
        $this->getEntityManager()->beginTransaction();
    }

    public function commit()
    {
        $this->getEntityManager()->commit();
    }

    public function rollback()
    {
        $this->getEntityManager()->rollback();
    }

    public function getRefdataReference($id)
    {
        return $this->getReference(RefDataEntity::class, $id);
    }

    public function getCategoryReference($id)
    {
        return $this->getReference(Category::class, $id);
    }

    public function getSubCategoryReference($id)
    {
        return $this->getReference(SubCategory::class, $id);
    }

    public function getReference($entityClass, $id)
    {
        return $this->getEntityManager()->getReference($entityClass, $id);
    }

    /**
     * @return EntityManagerInterface
     */
    protected function getEntityManager()
    {
        return $this->em;
    }

    /**
     * @return \Dvsa\Olcs\Api\Domain\QueryBuilder
     */
    protected function getQueryBuilder()
    {
        return $this->queryBuilder;
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function createQueryBuilder()
    {
        return $this->getEntityManager()->getRepository($this->entity)->createQueryBuilder($this->alias);
    }

    /**
     * @NOTE This method can be overridden to extend the default resource bundle
     *
     * @param QueryBuilder $qb
     * @param QryCmd $query
     * @return QueryBuilder
     */
    protected function buildDefaultQuery(QueryBuilder $qb, $id)
    {
        return $this->getQueryBuilder()->modifyQuery($qb)->withRefdata()->byId($id);
    }

    /**
     * @param QueryBuilder $qb
     * @param QueryInterface $query
     */
    protected function buildDefaultListQuery(QueryBuilder $qb, QueryInterface $query)
    {
        $queryBuilderHelper = $this->getQueryBuilder()->modifyQuery($qb);
        $queryBuilderHelper->withRefdata();

        if ($query instanceof PagedQueryInterface) {
            $queryBuilderHelper->paginate($query->getPage(), $query->getLimit());
        }

        if ($query instanceof OrderedQueryInterface) {
            $queryBuilderHelper->order($query->getSort(), $query->getOrder());
        }
    }
}
