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

    public function getRefdataReference($id);

    public function getReference($entityClass, $id);
}
