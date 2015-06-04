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
        throw new Exception\RuntimeException('This repository cannot delete');
    }

    public function fetchById($id, $hydrateMode = Query::HYDRATE_OBJECT, $version = null)
    {
        throw new Exception\RuntimeException('This repository cannot query');
    }

    /**
     * @param QueryInterface $query
     * @param int $hydrateMode
     * @return array
     */
    public function fetchList(QueryInterface $query, $hydrateMode = Query::HYDRATE_ARRAY)
    {
        throw new Exception\RuntimeException('This repository cannot query');
    }

    /**
     * @param QueryInterface $query
     */
    public function fetchCount(QueryInterface $query)
    {
        throw new Exception\RuntimeException('This repository cannot query');
    }

    public function lock($entity, $version)
    {
        throw new Exception\RuntimeException('This repository cannot lock');
    }

    public function save($entity)
    {
        throw new Exception\RuntimeException('This repository cannot save');
    }

    public function delete($entity)
    {
        throw new Exception\RuntimeException('This repository cannot delete');
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

}
