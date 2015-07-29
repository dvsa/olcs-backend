<?php

/**
 * Abstract Readonly Repository
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Dvsa\Olcs\Api\Domain\QueryBuilderInterface;
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
        $qb = $this->createQueryBuilder();

        $this->buildDefaultQuery($qb, $id);

        $this->applyFetchJoins($qb);

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
        $this->applyListJoins($qb);
        $this->applyListFilters($qb, $query);

        return $this->fetchPaginatedList($qb, $hydrateMode);
    }

    /**
     * Abstracted paginator logic so it can be re-used with alternative queries
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

        return $this->fetchPaginatedCount($qb);
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
        return $this->getEntityManager()->getRepository($this->entity)->createQueryBuilder($this->alias);
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
     */
    protected function buildDefaultListQuery(QueryBuilder $qb, QueryInterface $query)
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
                for ($i = 0; $i < count($sortColumns); $i++) {
                    // if multiple order value doesn'y exist then use the first one
                    $order = isset($orderColumns[$i]) ? $orderColumns[$i] : $orderColumns[0];
                    $queryBuilderHelper->order($sortColumns[$i], $order);
                }
            }
        }
    }

    /**
     * Wrap paginator instantiation, mainly for unit testing
     */
    protected function getPaginator($query)
    {
        return new Paginator($query);
    }
}
