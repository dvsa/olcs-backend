<?php

namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Dvsa\Olcs\Api\Domain\DbQueryServiceManager;
use Dvsa\Olcs\Api\Domain\QueryBuilderInterface;
use Dvsa\Olcs\Api\Domain\RepositoryServiceManager;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Api\Entity\System\SubCategory;
use Dvsa\Olcs\Api\Entity\System\RefData as RefDataEntity;
use Dvsa\Olcs\Api\Domain\Exception;
use Zend\Stdlib\ArraySerializableInterface as QryCmd;
use Dvsa\Olcs\Transfer\Query\OrderedQueryInterface;
use Dvsa\Olcs\Transfer\Query\PagedQueryInterface;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Abstract Readonly Repository
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractReadonlyRepository implements ReadonlyRepositoryInterface
{
    protected $entity = 'Define\Me';

    protected $alias = 'm';

    /**
     * Force paginated queries to fetch joined tables when paginating. Default is true.
     * @see http://doctrine-orm.readthedocs.io/projects/doctrine-orm/en/latest/tutorials/pagination.html
     *
     * @var bool
     */
    protected $fetchJoinCollection = true;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var \Dvsa\Olcs\Api\Domain\QueryBuilder
     */
    private $queryBuilder;

    private $references = [];

    /**
     * @var DbQueryServiceManager
     */
    private $dbQueryManager;

    public function __construct(
        EntityManagerInterface $em,
        QueryBuilderInterface $queryBuilder,
        DbQueryServiceManager $dbQueryManager
    ) {
        $this->em = $em;
        $this->queryBuilder = $queryBuilder;
        $this->dbQueryManager = $dbQueryManager;
    }

    /**
     * Disables SoftDeleteable filter, if enabled.
     * If list of entities is provided, the filter will only be disabled for those classes.
     *
     * @param array $entities List of class names
     *
     * @return void
     */
    public function disableSoftDeleteable(array $entities = null)
    {
        if ($this->getEntityManager()->getFilters()->isEnabled('soft-deleteable')) {
            if (!empty($entities)) {
                // disable soft-deleteable filtering for given entities only
                $filter = $this->getEntityManager()->getFilters()->getFilter('soft-deleteable');

                foreach ($entities as $entity) {
                    $filter->disableForEntity($entity);
                }
            } else {
                // disable soft-deleteable filtering for everything
                $this->getEntityManager()->getFilters()->disable('soft-deleteable');
            }
        }
    }

    /**
     * Enables SoftDeleteable filter
     *
     * @return void
     */
    public function enableSoftDeleteable()
    {
        // enable soft-deleteable filtering
        $this->getEntityManager()->getFilters()->enable('soft-deleteable');
    }

    /**
     * Called from the factory to allow additional services to be injected
     *
     * @param RepositoryServiceManager $serviceManager
     */
    public function initService(RepositoryServiceManager $serviceManager)
    {
        // no-op
    }

    public function __call($name, $args)
    {
        // fetchByFoo => WHERE alias.foo = $args[0]
        if (preg_match('/^fetchBy([a-zA-Z]+)$/', $name, $matches)) {
            return $this->fetchByX(lcfirst($matches[1]), $args);
        }

        if (preg_match('/^fetchOneBy([a-zA-Z]+)$/', $name, $matches)) {
            return $this->fetchOneByX(lcfirst($matches[1]), $args);
        }

        throw new \RuntimeException("Method '{$name}' not found on the Repository.");
    }

    /**
     * @return DbQueryServiceManager
     */
    protected function getDbQueryManager()
    {
        return $this->dbQueryManager;
    }

    protected function fetchByX($fetchBy, $args)
    {
        $qb = $this->createFetchByXxQuery($fetchBy, $args);

        if (empty($args[1])) {
            $hydrateMode = Query::HYDRATE_OBJECT;
        } else {
            $hydrateMode = $args[1];
        }

        return $qb->getQuery()->getResult($hydrateMode);
    }

    protected function fetchOneByX($fetchBy, $args)
    {
        $qb = $this->createFetchByXxQuery($fetchBy, $args);

        if (empty($args[1])) {
            $hydrateMode = Query::HYDRATE_OBJECT;
        } else {
            $hydrateMode = $args[1];
        }

        try {
            return $qb->getQuery()->getSingleResult($hydrateMode);
        } catch (\Exception $ex) {
            throw new Exception\NotFoundException(
                sprintf('Resource not found (%s %s %s)', $this->entity, $fetchBy, (string)$args[0])
            );
        }
    }

    protected function createFetchByXxQuery($fetchBy, $args)
    {
        // If the property doesn't exist
        if (!property_exists($this->entity, $fetchBy)) {
            return false;
        }

        if (empty($args)) {
            return false;
        }

        $value = $args[0];

        $qb = $this->createQueryBuilder();

        switch (true) {
            case is_array($value):
                $qb->andWhere(
                    $qb->expr()->in($this->alias . '.' . $fetchBy, $value)
                );
                break;
            case is_int($value):
                $qb->andWhere(
                    $qb->expr()->eq($this->alias . '.' . $fetchBy, $value)
                );
                break;
            case is_string($value):
                $qb->andWhere(
                    $qb->expr()->eq($this->alias . '.' . $fetchBy, ':' . $fetchBy)
                )->setParameter($fetchBy, $value);
                break;
        }

        return $qb;
    }

    /**
     * Fetch the default record by its id
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
        // If we are not locking and requesting an object, check the cache first
        $cache = ($version === null && $hydrateMode === Query::HYDRATE_OBJECT);
        if ($cache && isset($this->references[$id])) {

            return $this->references[$id];
        }

        $qb = $this->createQueryBuilder();

        $this->buildDefaultQuery($qb, $id);

        $this->applyFetchJoins($qb);

        $results = $qb->getQuery()->getResult($hydrateMode);

        if (empty($results)) {
            throw new Exception\NotFoundException(
                sprintf('Resource not found (%s id %s)', $this->entity, $id)
            );
        }

        if ($hydrateMode === Query::HYDRATE_OBJECT && $version !== null) {
            $this->lock($results[0], $version);
        }

        if ($cache) {
            $this->references[$id] = $results[0];
        }

        return $results[0];
    }

    /**
     * Fetch by ids
     *
     * @param array $ids
     * @param int $hydrateMode
     * @return mixed
     */
    public function fetchByIds(array $ids, $hydrateMode = Query::HYDRATE_OBJECT)
    {
        if (empty($ids)) {
            return [];
        }

        $qb = $this->createQueryBuilder();

        $this->getQueryBuilder()
            ->modifyQuery($qb)
            ->filterByIds($ids);

        return $qb->getQuery()->getResult($hydrateMode);
    }

    /**
     * @param QueryInterface $query
     * @param int $hydrateMode
     *
     * @return \ArrayIterator|\Traversable
     */
    public function fetchList(QueryInterface $query, $hydrateMode = Query::HYDRATE_ARRAY)
    {
        $qb = $this->createQueryBuilder();

        $this->buildDefaultListQuery($qb, $query);
        $this->applyListJoins($qb);
        $this->applyListFilters($qb, $query);

        return $this->fetchPaginatedList($qb, $hydrateMode);
    }

    /**
     * Abstracted paginator logic so it can be re-used with alternative queries
     *
     * @param QueryBuilder $qb
     * @param int          $hydrateMode
     *
     * @return \ArrayIterator|\Traversable
     */
    public function fetchPaginatedList(QueryBuilder $qb, $hydrateMode = Query::HYDRATE_ARRAY)
    {
        $query = $qb->getQuery();
        $query->setHydrationMode($hydrateMode);

        $paginator = $this->getPaginator($query);
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
        $this->applyListJoins($qb);
        $this->applyListFilters($qb, $query);

        // order is not important for count but slows down the query (a lot!)
        $qb->resetDQLPart('orderBy');

        return $this->fetchPaginatedCount($qb);
    }

    /**
     * Does the have any rows
     *
     * @param QueryInterface $query
     *
     * @return bool
     */
    public function hasRows(QueryInterface $query)
    {
        $qb = $this->createQueryBuilder();

        $this->buildDefaultListQuery($qb, $query);
        $this->applyListJoins($qb);
        $this->applyListFilters($qb, $query);

        // order is not important for count but slows down the query (a lot!)
        $qb->resetDQLPart('orderBy');

        $qb->setMaxResults(1);

        return count($qb->getQuery()->getResult()) === 1;
    }

    /**
     * Abstracted the count functionality so it can be re-used with alternative queries
     */
    public function fetchPaginatedCount(QueryBuilder $qb)
    {
        $query = $qb->getQuery();

        $paginator = $this->getPaginator($query);
        return $paginator->count();
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function applyListFilters(QueryBuilder $qb, QueryInterface $query)
    {

    }

    /**
     * Override to add additional data to the default fetchList() method
     * @param QueryBuilder $qb
     * @inheritdoc
     */
    protected function applyListJoins(QueryBuilder $qb)
    {

    }

    /**
     * Override to add additional data to the default fetchById() method
     * @param QueryBuilder $qb
     * @inheritdoc
     */
    protected function applyFetchJoins(QueryBuilder $qb)
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

    /**
     * @param string $id
     * @return RefDataEntity|null
     */
    public function getRefdataReference($id)
    {
        return $this->getReference(RefDataEntity::class, $id);
    }

    /**
     * @param int $id
     * @return Category|null
     */
    public function getCategoryReference($id)
    {
        return $this->getReference(Category::class, $id);
    }

    /**
     * @param $id
     * @return SubCategory|null
     */
    public function getSubCategoryReference($id)
    {
        return $this->getReference(SubCategory::class, $id);
    }

    /**
     * Get Reference
     *
     * @param string     $entityClass Entity class FQN
     * @param string|int $id          id
     *
     * @return null|$entityClass
     */
    public function getReference($entityClass, $id)
    {
        return !empty($id) ? $this->getEntityManager()->getReference($entityClass, $id) : null;
    }

    public function generateRefdataArrayCollection($ids)
    {
        $refDataArray = [];
        if (!empty($ids)) {
            foreach ($ids as $id) {
                $refDataArray[] = $this->getRefdataReference($id);
            }
        }
        return new ArrayCollection($refDataArray);
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
        return $this->getRepository()->createQueryBuilder($this->alias);
    }

    protected function getRepository()
    {
        return $this->getEntityManager()->getRepository($this->entity);
    }

    /**
     * @NOTE This method can be overridden to extend the default resource bundle
     *
     * @param QueryBuilder $qb
     * @param int $id
     * @return \Dvsa\Olcs\Api\Domain\QueryBuilder
     */
    protected function buildDefaultQuery(QueryBuilder $qb, $id)
    {
        return $this->getQueryBuilder()->modifyQuery($qb)->withRefdata()->byId($id);
    }

    protected function createDefaultListQuery(QueryInterface $query)
    {
        $qb = $this->createQueryBuilder();
        $this->buildDefaultListQuery($qb, $query);

        return $qb;
    }

    /**
     * @param QueryBuilder $qb
     * @param QueryInterface $query
     * @param array $compositeFields
     */
    protected function buildDefaultListQuery(QueryBuilder $qb, QueryInterface $query, $compositeFields = [])
    {
        $queryBuilderHelper = $this->getQueryBuilder()->modifyQuery($qb);
        $queryBuilderHelper->withRefdata();

        if ($query instanceof PagedQueryInterface) {
            $queryBuilderHelper->paginate($query->getPage(), $query->getLimit());
        }

        if ($query instanceof OrderedQueryInterface) {
            if (!empty($query->getSort())) {
                // allow ordering by multiple columns
                $sortColumns = explode(',', $query->getSort());
                $orderColumns = explode(',', $query->getOrder());

                foreach ($sortColumns as $i => $column) {
                    // if multiple order value doesn't exist then use the first one
                    $order = isset($orderColumns[$i]) ? $orderColumns[$i] : $orderColumns[0];

                    $queryBuilderHelper->order($column, $order, $compositeFields);
                }
            }
        }
    }

    /**
     * Wrap paginator instantiation, mainly for unit testing
     */
    protected function getPaginator($query)
    {
        return new Paginator($query, $this->fetchJoinCollection);
    }
}
