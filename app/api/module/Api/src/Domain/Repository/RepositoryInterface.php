<?php

/**
 * Repository Interface
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\Query;
use Zend\Stdlib\ArraySerializableInterface;
use Zend\Stdlib\ArraySerializableInterface as QryCmd;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Api\Entity\System\SubCategory;

/**
 * Repository Interface
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
interface RepositoryInterface
{
    public function lock($entity, $version);

    public function save($entity);

    public function beginTransaction();

    public function commit();

    public function rollback();

    public function fetchUsingId(QryCmd $query, $hydrateMode = Query::HYDRATE_ARRAY, $version = null);

    /**
     * @param $id
     * @return RefData
     */
    public function getRefdataReference($id);

    /**
     * @param $id
     * @return Category
     */
    public function getCategoryReference($id);

    /**
     * @param $id
     * @return SubCategory
     */
    public function getSubCategoryReference($id);

    public function getReference($entityClass, $id);
}
