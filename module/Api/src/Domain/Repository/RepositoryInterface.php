<?php

/**
 * Repository Interface
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Zend\Stdlib\ArraySerializableInterface;

/**
 * Repository Interface
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
interface RepositoryInterface
{
    public function fetchUsingId(ArraySerializableInterface $query);

    public function getRefdataReference($id);

    public function getReference($entityClass, $id);
}
